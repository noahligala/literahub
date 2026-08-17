<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
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
            'student',
        ]);
    }

    public function view(
        User $user,
        Author $author
    ): bool {
        /*
         * Author can always see own profile.
         */
        if (
            $user->hasRole('author')
            && $author->user_id === $user->id
        ) {
            return true;
        }

        /*
         * Verified author profiles may be viewed
         * wherever catalogue metadata is exposed.
         */
        if ($author->status === 'verified') {
            return true;
        }

        return $user->hasRole(
            'content_manager'
        );
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
        Author $author
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        return
            $user->hasRole('author')
            && $author->user_id === $user->id;
    }

    public function delete(
        User $user,
        Author $author
    ): bool {
        if (!$user->hasRole('content_manager')) {
            return false;
        }

        /*
         * Preserve authorship records when works
         * already exist.
         */
        return !$author
            ->books()
            ->exists();
    }

    public function uploadBook(
        User $user,
        Author $author
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        return
            $user->hasRole('author')
            && $author->user_id === $user->id
            && $author->status === 'verified';
    }

    public function manageRights(
        User $user,
        Author $author
    ): bool {
        if ($user->hasRole('content_manager')) {
            return true;
        }

        return
            $user->hasRole('author')
            && $author->user_id === $user->id;
    }

    public function grantLicense(
        User $user,
        Author $author
    ): bool {
        /*
         * This only allows author-level licensing.
         *
         * Book/publisher rights must still be checked
         * before a licence is actually created.
         */
        return
            $user->hasRole('author')
            && $author->user_id === $user->id
            && $author->status === 'verified';
    }
}