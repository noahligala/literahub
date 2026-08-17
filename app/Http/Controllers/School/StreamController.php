<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StreamController extends Controller
{
    private function school(Request $request): School
    {
        return $request
            ->user()
            ->schools()
            ->firstOrFail();
    }

    public function create(
        Request $request,
        int $class
    ): View {
        $school = $this->school($request);

        $class = $school
            ->classes()
            ->whereKey($class)
            ->firstOrFail();

        $teachers = $school
            ->users()
            ->wherePivot('role', 'teacher')
            ->wherePivot('status', 'active')
            ->orderBy('users.name')
            ->get();

        return view(
            'school.classes.streams.create',
            compact(
                'school',
                'class',
                'teachers'
            )
        );
    }

    public function store(
        Request $request,
        int $class
    ): RedirectResponse {
        $school = $this->school($request);

        $class = $school
            ->classes()
            ->whereKey($class)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'teacher_id' => [
                'nullable',
                'integer',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $class
            ->streams()
            ->create($validated);

        return redirect()
            ->route(
                'school.classes.show',
                $class
            )
            ->with(
                'success',
                'Stream created successfully.'
            );
    }

    public function edit(
        Request $request,
        int $stream
    ): View {
        $school = $this->school($request);

        $stream = Stream::query()
            ->whereHas(
                'schoolClass',
                fn ($query) =>
                $query->where(
                    'school_id',
                    $school->id
                )
            )
            ->findOrFail($stream);

        $class = $stream->schoolClass;

        $teachers = $school
            ->users()
            ->wherePivot('role', 'teacher')
            ->orderBy('users.name')
            ->get();

        return view(
            'school.classes.streams.edit',
            compact(
                'school',
                'class',
                'stream',
                'teachers'
            )
        );
    }

    public function update(
        Request $request,
        int $stream
    ): RedirectResponse {
        $school = $this->school($request);

        $stream = Stream::query()
            ->whereHas(
                'schoolClass',
                fn ($query) =>
                $query->where(
                    'school_id',
                    $school->id
                )
            )
            ->findOrFail($stream);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'teacher_id' => [
                'nullable',
                'integer',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $stream->update($validated);

        return redirect()
            ->route(
                'school.classes.show',
                $stream->schoolClass
            )
            ->with(
                'success',
                'Stream updated successfully.'
            );
    }
}