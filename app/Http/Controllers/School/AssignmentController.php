<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    private function assignment(
        School $school,
        int|string $id
    ): Assignment {
        return $school
            ->assignments()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function index(Request $request): View
    {
        $school = $this->school($request);

        $assignments = $school
            ->assignments()
            ->with([
                'schoolClass',
                'creator',
            ])
            ->withCount('students')
            ->latest()
            ->paginate(20);

        return view(
            'school.assignments.index',
            compact(
                'school',
                'assignments'
            )
        );
    }

    public function create(Request $request): View
    {
        $school = $this->school($request);

        $classes = $school
            ->classes()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'school.assignments.create',
            compact(
                'school',
                'classes'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $school = $this->school($request);

        $validated = $this->validated(
            $request,
            $school
        );

        $assignment = $school
            ->assignments()
            ->create([
                ...$validated,

                'creator_id' =>
                    $request->user()->id,
            ]);

        $class = $school
            ->classes()
            ->findOrFail(
                $validated[
                    'school_class_id'
                ]
            );

        $assignment
            ->students()
            ->sync(
                $class
                    ->students()
                    ->pluck('users.id')
                    ->all()
            );

        return redirect()
            ->route(
                'school.assignments.show',
                $assignment
            )
            ->with(
                'success',
                'Assignment created successfully.'
            );
    }

    public function show(
        Request $request,
        int $assignment
    ): View {
        $school = $this->school($request);

        $assignment = $this->assignment(
            $school,
            $assignment
        );

        $assignment->load([
            'schoolClass',
            'creator',
            'students',
        ]);

        return view(
            'school.assignments.show',
            compact(
                'school',
                'assignment'
            )
        );
    }

    public function edit(
        Request $request,
        int $assignment
    ): View {
        $school = $this->school($request);

        $assignment = $this->assignment(
            $school,
            $assignment
        );

        $classes = $school
            ->classes()
            ->orderBy('name')
            ->get();

        return view(
            'school.assignments.edit',
            compact(
                'school',
                'assignment',
                'classes'
            )
        );
    }

    public function update(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school = $this->school($request);

        $assignment = $this->assignment(
            $school,
            $assignment
        );

        $validated = $this->validated(
            $request,
            $school
        );

        $assignment->update(
            $validated
        );

        $class = $school
            ->classes()
            ->findOrFail(
                $validated[
                    'school_class_id'
                ]
            );

        $assignment
            ->students()
            ->sync(
                $class
                    ->students()
                    ->pluck('users.id')
                    ->all()
            );

        return redirect()
            ->route(
                'school.assignments.show',
                $assignment
            )
            ->with(
                'success',
                'Assignment updated successfully.'
            );
    }

    public function destroy(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $school = $this->school($request);

        $assignment = $this->assignment(
            $school,
            $assignment
        );

        $assignment->update([
            'status' => 'archived',
        ]);

        return redirect()
            ->route(
                'school.assignments.index'
            )
            ->with(
                'success',
                'Assignment archived.'
            );
    }

    private function validated(
        Request $request,
        School $school
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'school_class_id' => [
                'required',

                Rule::exists(
                    'school_classes',
                    'id'
                )->where(
                    fn ($query) =>
                    $query->where(
                        'school_id',
                        $school->id
                    )
                ),
            ],

            'resource_id' => [
                'nullable',
                'integer',
            ],

            'instructions' => [
                'nullable',
                'string',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',

                Rule::in([
                    'draft',
                    'published',
                    'closed',
                    'archived',
                ]),
            ],
        ]);
    }
}