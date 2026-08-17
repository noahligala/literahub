<div class="form-grid">

    <div class="form-group">

        <label>
            Stream Name
        </label>

        <input
            name="name"
            value="{{ old(
                'name',
                $stream->name ?? ''
            ) }}"
            placeholder="East"
            required
        >

    </div>

    <div class="form-group">

        <label>
            Class Teacher
        </label>

        <select name="teacher_id">

            <option value="">
                Not Assigned
            </option>

            @foreach($teachers as $teacher)

                <option
                    value="{{ $teacher->id }}"
                    @selected(
                        old(
                            'teacher_id',
                            $stream->teacher_id ?? null
                        ) == $teacher->id
                    )
                >
                    {{ $teacher->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label>Status</label>

        <select name="status">

            <option
                value="active"
                @selected(
                    old(
                        'status',
                        $stream->status ?? 'active'
                    ) === 'active'
                )
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(
                    old(
                        'status',
                        $stream->status ?? 'active'
                    ) === 'inactive'
                )
            >
                Inactive
            </option>

        </select>

    </div>

</div>