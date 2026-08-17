<?php

namespace App\Policies;

use App\Models\SchoolBookLicense;
use App\Models\User;

class SchoolBookLicensePolicy
{
    public function before(
        User $user,
        string $ability
    ): ?bool {
        if (
            $user->hasAnyRole([
                'super_admin',
                'platform_admin',
            ])
        ) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View All Licences
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'content_manager',
            'author',
            'school_admin',
            'finance',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | View Licence
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        SchoolBookLicense $license
    ): bool {
        if (
            $user->hasAnyRole([
                'content_manager',
                'finance',
            ])
        ) {
            return true;
        }

        /*
         * School admins may see licences belonging
         * to their own institution.
         */
        if ($user->hasRole('school_admin')) {
            return $user
                ->schools()
                ->where(
                    'schools.id',
                    $license->school_id
                )
                ->exists();
        }

        /*
         * Author may inspect a licence they issued.
         */
        if (
            $user->hasRole('author')
            && $license->author_id
        ) {
            return $license
                ->author()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Licence
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'content_manager',
            'author',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Licence
    |--------------------------------------------------------------------------
    */

    public function update(
        User $user,
        SchoolBookLicense $license
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        /*
         * Author can update only licences that
         * were granted directly through that author.
         */
        if (
            $user->hasRole('author')
            && $license->author_id
        ) {
            return $license
                ->author()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Revoke Licence
    |--------------------------------------------------------------------------
    */

    public function revoke(
        User $user,
        SchoolBookLicense $license
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        if (
            $user->hasRole('author')
            && $license->author_id
        ) {
            return $license
                ->author()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Licence
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $user,
        SchoolBookLicense $license
    ): bool {
        /*
         * Licence records are commercially and legally
         * important. Prefer revocation over deletion.
         */
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Renew Licence
    |--------------------------------------------------------------------------
    */

    public function renew(
        User $user,
        SchoolBookLicense $license
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        if (
            $user->hasRole('author')
            && $license->author_id
        ) {
            return $license
                ->author()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Suspend Licence
    |--------------------------------------------------------------------------
    */

    public function suspend(
        User $user,
        SchoolBookLicense $license
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }
}