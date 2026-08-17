<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\School;
use App\Models\SchoolBookLicense;
use Illuminate\Database\Eloquent\Builder;

class BookLicenseService
{
    public function activeLicense(
        School $school,
        Book $book
    ): ?SchoolBookLicense {
        return SchoolBookLicense::query()
            ->where(
                'school_id',
                $school->id
            )
            ->where(
                'book_id',
                $book->id
            )
            ->where(
                'status',
                'active'
            )
            ->where(
                'starts_at',
                '<=',
                now()
            )
            ->where(
                function (Builder $query) {
                    $query
                        ->whereNull(
                            'expires_at'
                        )
                        ->orWhere(
                            'expires_at',
                            '>',
                            now()
                        );
                }
            )
            ->first();
    }

    public function hasActiveLicense(
        School $school,
        Book $book
    ): bool {
        return $this->activeLicense(
            $school,
            $book
        ) !== null;
    }

    public function licensedBooksQuery(
        School $school
    ): Builder {
        return Book::query()
            ->whereHas(
                'licenses',
                function (Builder $query) use ($school) {
                    $query
                        ->where(
                            'school_id',
                            $school->id
                        )
                        ->where(
                            'status',
                            'active'
                        )
                        ->where(
                            'starts_at',
                            '<=',
                            now()
                        )
                        ->where(
                            function (Builder $query) {
                                $query
                                    ->whereNull(
                                        'expires_at'
                                    )
                                    ->orWhere(
                                        'expires_at',
                                        '>',
                                        now()
                                    );
                            }
                        );
                }
            );
    }

    public function expireInvalidLicenses(): int
    {
        return SchoolBookLicense::query()
            ->where(
                'status',
                'active'
            )
            ->whereNotNull(
                'expires_at'
            )
            ->where(
                'expires_at',
                '<=',
                now()
            )
            ->update([
                'status' =>
                    'expired',
            ]);
    }

    public function revoke(
        SchoolBookLicense $license,
        ?int $revokedBy = null
    ): SchoolBookLicense {
        $license->update([
            'status' =>
                'revoked',

            'revoked_at' =>
                now(),

            'revoked_by' =>
                $revokedBy,
        ]);

        return $license->refresh();
    }
}