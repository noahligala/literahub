<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\SchoolBookLicense;
use App\Models\User;

class BookRightsService
{
    public function canRead(
        User $user,
        Book $book,
        ?SchoolBookLicense $license = null
    ): bool {
        if (!$book->allow_online_reading) {
            return false;
        }

        if ($license && !$license->isActive()) {
            return false;
        }

        if ($user->hasAnyRole([
            'super_admin',
            'platform_admin',
            'content_manager',
        ])) {
            return true;
        }

        if ($user->hasRole('author')) {
            return $book
                ->authors()
                ->where('user_id', $user->id)
                ->exists();
        }

        if ($user->hasRole('teacher')) {
            return $license
                ? $license->allow_teacher_reading
                : false;
        }

        if ($user->hasRole('student')) {
            return $license
                ? $license->allow_student_reading
                : false;
        }

        return false;
    }

    public function canDownload(
        Book $book,
        ?SchoolBookLicense $license = null
    ): bool {
        if (!$book->allow_download) {
            return false;
        }

        if (!$license || !$license->isActive()) {
            return false;
        }

        return (bool) $license->allow_download;
    }

    public function canPrint(
        Book $book,
        ?SchoolBookLicense $license = null
    ): bool {
        if (!$book->allow_print) {
            return false;
        }

        if (!$license || !$license->isActive()) {
            return false;
        }

        return (bool) $license->allow_print;
    }

    public function canBorrow(
        Book $book,
        ?SchoolBookLicense $license = null
    ): bool {
        if (!$book->allow_student_borrowing) {
            return false;
        }

        if (!$license || !$license->isActive()) {
            return false;
        }

        return (bool) $license->allow_student_borrowing;
    }

    public function canTeacherAssign(
        Book $book,
        ?SchoolBookLicense $license = null
    ): bool {
        if (!$book->allow_teacher_assignment) {
            return false;
        }

        if (!$license || !$license->isActive()) {
            return false;
        }

        return (bool) $license->allow_teacher_assignment;
    }

    public function effectiveRights(
        Book $book,
        ?SchoolBookLicense $license = null
    ): array {
        return [
            'read' =>
                $license
                    ? (
                        $book->allow_online_reading
                        && $license->isActive()
                    )
                    : false,

            'download' =>
                $this->canDownload(
                    $book,
                    $license
                ),

            'print' =>
                $this->canPrint(
                    $book,
                    $license
                ),

            'borrow' =>
                $this->canBorrow(
                    $book,
                    $license
                ),

            'teacher_assign' =>
                $this->canTeacherAssign(
                    $book,
                    $license
                ),
        ];
    }
}