<?php

namespace App\Services\Reader;

use App\Models\RegisteredDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReaderDeviceService
{
    private const COOKIE_NAME =
        'literahub_reader_device';


    /**
     * Resolve an existing registered device or register a new one.
     */
    public function resolveOrRegister(
        User $user,
        Request $request
    ): RegisteredDevice {
        $cookie =
            $request->cookie(
                self::COOKIE_NAME
            );


        /*
        |--------------------------------------------------------------------------
        | Existing Device
        |--------------------------------------------------------------------------
        */

        if (
            filled($cookie)
            &&
            str_contains(
                $cookie,
                '.'
            )
        ) {
            [
                $deviceUuid,
                $rawSecret,
            ] = explode(
                '.',
                $cookie,
                2
            );


            $device =
                RegisteredDevice::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'device_uuid',
                        $deviceUuid
                    )
                    ->first();


            if ($device) {
                if ($device->revoked_at) {
                    throw new HttpException(
                        403,
                        'This registered device has been revoked.'
                    );
                }


                if (
                    filled(
                        $device->device_token_hash
                    )
                    &&
                    ! hash_equals(
                        $device->device_token_hash,
                        hash(
                            'sha256',
                            $rawSecret
                        )
                    )
                ) {
                    throw new HttpException(
                        403,
                        'Device credentials are invalid.'
                    );
                }


                $this->updateDeviceActivity(
                    $device,
                    $request
                );


                return $device;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Device Limit Before Registration
        |--------------------------------------------------------------------------
        */

        $limit =
            $this->deviceLimit(
                $user
            );


        $activeDevices =
            RegisteredDevice::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->whereNull(
                    'revoked_at'
                )
                ->count();


        if (
            $limit > 0
            &&
            $activeDevices >= $limit
        ) {
            throw new HttpException(
                403,
                "Your account has reached its {$limit}-device limit."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Register Device
        |--------------------------------------------------------------------------
        */

        $deviceUuid =
            (string) Str::uuid();


        $rawSecret =
            Str::random(80);


        $device =
            RegisteredDevice::query()
                ->create([
                    'user_id' =>
                        $user->id,

                    'device_uuid' =>
                        $deviceUuid,

                    'device_token_hash' =>
                        hash(
                            'sha256',
                            $rawSecret
                        ),

                    'device_name' =>
                        $this->deviceName(
                            $request
                        ),

                    'device_type' =>
                        $this->deviceType(
                            $request
                        ),

                    'browser' =>
                        $this->browser(
                            $request
                        ),

                    'platform' =>
                        $this->platform(
                            $request
                        ),

                    'last_ip_address' =>
                        $request->ip(),

                    'last_user_agent' =>
                        $request->userAgent(),

                    'fingerprint_hash' =>
                        $this->fingerprint(
                            $request
                        ),

                    'first_seen_at' =>
                        now(),

                    'last_seen_at' =>
                        now(),
                ]);


        /*
        |--------------------------------------------------------------------------
        | Queue Encrypted HTTP-only Cookie
        |--------------------------------------------------------------------------
        */

        Cookie::queue(
            cookie(
                self::COOKIE_NAME,
                $deviceUuid
                . '.'
                . $rawSecret,
                525600, // one year
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'strict'
            )
        );


        return $device;
    }


    /**
     * Final device check.
     */
    public function assertDeviceAllowed(
        User $user,
        RegisteredDevice $device
    ): void {
        if (
            (int) $device->user_id
            !==
            (int) $user->id
        ) {
            throw new HttpException(
                403,
                'Device does not belong to this account.'
            );
        }


        if ($device->revoked_at) {
            throw new HttpException(
                403,
                'This device has been revoked.'
            );
        }


        $limit =
            $this->deviceLimit(
                $user
            );


        $activeCount =
            RegisteredDevice::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->whereNull(
                    'revoked_at'
                )
                ->count();


        if (
            $limit > 0
            &&
            $activeCount > $limit
        ) {
            throw new HttpException(
                403,
                'Registered device limit has been exceeded.'
            );
        }
    }


    /**
     * Verify that a request is coming from the device
     * attached to a ReaderSession.
     */
    public function requestMatchesDevice(
        RegisteredDevice $device,
        Request $request
    ): bool {
        $cookie =
            $request->cookie(
                self::COOKIE_NAME
            );


        if (
            ! filled($cookie)
            ||
            ! str_contains(
                $cookie,
                '.'
            )
        ) {
            return false;
        }


        [
            $uuid,
            $secret,
        ] = explode(
            '.',
            $cookie,
            2
        );


        if (
            ! hash_equals(
                (string) $device->device_uuid,
                (string) $uuid
            )
        ) {
            return false;
        }


        if (
            ! filled(
                $device->device_token_hash
            )
        ) {
            return false;
        }


        return hash_equals(
            $device->device_token_hash,
            hash(
                'sha256',
                $secret
            )
        );
    }


    private function updateDeviceActivity(
        RegisteredDevice $device,
        Request $request
    ): void {
        $device->forceFill([
            'last_seen_at' =>
                now(),

            'last_ip_address' =>
                $request->ip(),

            'last_user_agent' =>
                $request->userAgent(),

            'browser' =>
                $this->browser(
                    $request
                ),

            'platform' =>
                $this->platform(
                    $request
                ),

            'device_type' =>
                $this->deviceType(
                    $request
                ),
        ])->save();
    }


    private function deviceLimit(
        User $user
    ): int {
        foreach (
            [
                'school_admin',
                'teacher',
                'student',
                'individual_subscriber',
            ]
            as $role
        ) {
            if (
                $user->hasRole(
                    $role
                )
            ) {
                return (int) config(
                    "reader.devices.{$role}",
                    2
                );
            }
        }


        /*
         * Platform staff are not restricted by student device limits.
         */
        return 5;
    }


    private function fingerprint(
        Request $request
    ): string {
        return hash(
            'sha256',
            implode(
                '|',
                [
                    $request->userAgent()
                        ?? '',

                    $request->header(
                        'Sec-CH-UA-Platform',
                        ''
                    ),

                    $request->header(
                        'Sec-CH-UA-Mobile',
                        ''
                    ),

                    $request->header(
                        'Accept-Language',
                        ''
                    ),
                ]
            )
        );
    }


    private function deviceType(
        Request $request
    ): string {
        $agent =
            strtolower(
                $request->userAgent()
                ?? ''
            );


        if (
            str_contains(
                $agent,
                'ipad'
            )
            ||
            str_contains(
                $agent,
                'tablet'
            )
        ) {
            return 'tablet';
        }


        if (
            str_contains(
                $agent,
                'mobile'
            )
            ||
            str_contains(
                $agent,
                'android'
            )
            ||
            str_contains(
                $agent,
                'iphone'
            )
        ) {
            return 'mobile';
        }


        return 'desktop';
    }


    private function browser(
        Request $request
    ): string {
        $agent =
            strtolower(
                $request->userAgent()
                ?? ''
            );


        return match (true) {
            str_contains(
                $agent,
                'edg/'
            ) => 'Edge',

            str_contains(
                $agent,
                'chrome/'
            ) => 'Chrome',

            str_contains(
                $agent,
                'firefox/'
            ) => 'Firefox',

            str_contains(
                $agent,
                'safari/'
            )
                &&
            ! str_contains(
                $agent,
                'chrome/'
            ) => 'Safari',

            default =>
                'Other',
        };
    }


    private function platform(
        Request $request
    ): string {
        $agent =
            strtolower(
                $request->userAgent()
                ?? ''
            );


        return match (true) {
            str_contains(
                $agent,
                'android'
            ) => 'Android',

            str_contains(
                $agent,
                'iphone'
            )
                ||
            str_contains(
                $agent,
                'ipad'
            ) => 'iOS',

            str_contains(
                $agent,
                'windows'
            ) => 'Windows',

            str_contains(
                $agent,
                'macintosh'
            )
                ||
            str_contains(
                $agent,
                'mac os'
            ) => 'macOS',

            str_contains(
                $agent,
                'linux'
            ) => 'Linux',

            default =>
                'Other',
        };
    }


    private function deviceName(
        Request $request
    ): string {
        return $this->browser($request)
            . ' on '
            . $this->platform($request);
    }
}