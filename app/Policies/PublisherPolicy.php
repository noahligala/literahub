<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
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

    public function viewAny(
        User $user
    ): bool {
        return $user->hasAnyRole([
            'content_manager',
            'author',
            'school_admin',
            'teacher',
        ]);
    }

    public function view(
        User $user,
        Publisher $publisher
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        if ($user->hasRole('author')) {
            return $publisher
                ->authors()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();
        }

        /*
         * School staff may view publisher details
         * related to licensed catalogue content.
         */
        if (
            $user->hasAnyRole([
                'school_admin',
                'teacher',
            ])
        ) {
            $schoolId = $user
                ->schools()
                ->value('schools.id');

            if (!$schoolId) {
                return false;
            }

            return $publisher
                ->schoolBookLicenses()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'status',
                    'active'
                )
                ->exists();
        }

        return false;
    }

    public function create(
        User $user
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }

    public function update(
        User $user,
        Publisher $publisher
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }

    public function delete(
        User $user,
        Publisher $publisher
    ): bool {
        if (!$user->hasRole('content_manager')) {
            return false;
        }

        /*
         * Do not remove publishers while catalogue
         * data or licences still depend on them.
         */
        return
            !$publisher->books()->exists()
            && !$publisher->schoolBookLicenses()->exists();
    }

    public function suspend(
        User $user,
        Publisher $publisher
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }

    public function manageAuthors(
        User $user,
        Publisher $publisher
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }

    public function manageLicenses(
        User $user,
        Publisher $publisher
    ): bool {
        return $user->hasRole(
            'content_manager'
        );
    }
}