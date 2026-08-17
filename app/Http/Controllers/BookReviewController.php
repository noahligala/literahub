<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\Library\BookReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BookReviewController extends Controller
{
    public function __construct(
        private readonly BookReviewService $reviews
    ) {
    }

    public function index(
        Request $request
    ): View {
        $user = $request->user();

        abort_unless(
            $user->hasAnyRole([
                'super_admin',
                'platform_admin',
                'content_manager',
            ]),
            403
        );

        $status =
            $request->query(
                'status',
                'under_review'
            );

        $books = Book::query()
            ->with([
                'publisher',
                'authors',
                'uploader',
            ])
            ->when(
                $status !== 'all',
                fn ($query) =>
                    $query->where(
                        'status',
                        $status
                    )
            )
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'books.review.index',
            compact(
                'books',
                'status'
            )
        );
    }

    public function show(
        Book $book
    ): View {
        Gate::authorize(
            'review',
            $book
        );

        $book->load([
            'publisher',
            'authors',
            'uploader',
            'reviewer',
        ]);

        return view(
            'books.review.show',
            compact('book')
        );
    }

    public function approve(
        Request $request,
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'review',
            $book
        );

        $validated =
            $request->validate([
                'review_notes' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
            ]);

        $this->reviews->approve(
            $book,
            $request->user(),
            $validated[
                'review_notes'
            ] ?? null
        );

        return back()->with(
            'success',
            'Book approved successfully.'
        );
    }

    public function publish(
        Request $request,
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'publish',
            $book
        );

        $this->reviews->publish(
            $book,
            $request->user()
        );

        return back()->with(
            'success',
            'Book published successfully.'
        );
    }

    public function requestChanges(
        Request $request,
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'review',
            $book
        );

        $validated =
            $request->validate([
                'review_notes' => [
                    'required',
                    'string',
                    'min:5',
                    'max:10000',
                ],
            ]);

        $this->reviews
            ->requestChanges(
                $book,
                $request->user(),
                $validated[
                    'review_notes'
                ]
            );

        return back()->with(
            'success',
            'Changes requested from the author.'
        );
    }

    public function reject(
        Request $request,
        Book $book
    ): RedirectResponse {
        Gate::authorize(
            'review',
            $book
        );

        $validated =
            $request->validate([
                'review_notes' => [
                    'required',
                    'string',
                    'min:5',
                    'max:10000',
                ],
            ]);

        $this->reviews->reject(
            $book,
            $request->user(),
            $validated[
                'review_notes'
            ]
        );

        return back()->with(
            'success',
            'Book rejected.'
        );
    }
}