<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookReviewService
{
    public function submit(
        Book $book
    ): Book {
        if (!in_array(
            $book->status,
            [
                'draft',
                'changes_requested',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'status' =>
                    'Only draft books or books requiring changes may be submitted for review.',
            ]);
        }

        $book->update([
            'status' =>
                'under_review',

            'submitted_at' =>
                now(),

            'reviewed_at' =>
                null,

            'reviewed_by' =>
                null,

            'review_notes' =>
                null,
        ]);

        return $book->refresh();
    }

    public function approve(
        Book $book,
        User $reviewer,
        ?string $notes = null
    ): Book {
        $this->ensureReviewer(
            $reviewer
        );

        if ($book->status !== 'under_review') {
            throw ValidationException::withMessages([
                'status' =>
                    'Only books under review may be approved.',
            ]);
        }

        $book->update([
            'status' =>
                'approved',

            'reviewed_at' =>
                now(),

            'reviewed_by' =>
                $reviewer->id,

            'review_notes' =>
                $notes,
        ]);

        return $book->refresh();
    }

    public function publish(
        Book $book,
        User $reviewer
    ): Book {
        $this->ensureReviewer(
            $reviewer
        );

        if (!in_array(
            $book->status,
            [
                'approved',
                'under_review',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'status' =>
                    'The book must be approved before publication.',
            ]);
        }

        return DB::transaction(
            function () use (
                $book,
                $reviewer
            ) {
                $book->update([
                    'status' =>
                        'published',

                    'reviewed_at' =>
                        $book->reviewed_at
                        ?? now(),

                    'reviewed_by' =>
                        $book->reviewed_by
                        ?? $reviewer->id,
                ]);

                return $book->refresh();
            }
        );
    }

    public function requestChanges(
        Book $book,
        User $reviewer,
        string $notes
    ): Book {
        $this->ensureReviewer(
            $reviewer
        );

        $book->update([
            'status' =>
                'changes_requested',

            'reviewed_at' =>
                now(),

            'reviewed_by' =>
                $reviewer->id,

            'review_notes' =>
                $notes,
        ]);

        return $book->refresh();
    }

    public function reject(
        Book $book,
        User $reviewer,
        string $notes
    ): Book {
        $this->ensureReviewer(
            $reviewer
        );

        $book->update([
            'status' =>
                'rejected',

            'reviewed_at' =>
                now(),

            'reviewed_by' =>
                $reviewer->id,

            'review_notes' =>
                $notes,
        ]);

        return $book->refresh();
    }

    private function ensureReviewer(
        User $reviewer
    ): void {
        if (!$reviewer->hasAnyRole([
            'super_admin',
            'platform_admin',
            'content_manager',
        ])) {
            abort(
                403,
                'You are not authorised to review books.'
            );
        }
    }
}