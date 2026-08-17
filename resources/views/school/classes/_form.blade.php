<div class="form-grid">

    <div class="form-group">
        <label>Class Name</label>

        <input
            name="name"
            value="{{ old(
                'name',
                $class->name ?? ''
            ) }}"
            placeholder="Form 3"
            required
        >
    </div>

    <div class="form-group">
        <label>Class Code</label>

        <input
            name="code"
            value="{{ old(
                'code',
                $class->code ?? ''
            ) }}"
            placeholder="F3"
        >
    </div>

    <div class="form-group">
        <label>Level</label>

        <input
            name="level"
            value="{{ old(
                'level',
                $class->level ?? ''
            ) }}"
            placeholder="Form 3"
        >
    </div>

    <div class="form-group">
        <label>Academic Year</label>

        <input
            name="academic_year"
            value="{{ old(
                'academic_year',
                $class->academic_year
                    ?? date('Y')
            ) }}"
        >
    </div>

    <div class="form-group">
        <label>Status</label>

        <select name="status">

            <option
                value="active"
                @selected(
                    old(
                        'status',
                        $class->status ?? 'active'
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
                        $class->status ?? 'active'
                    ) === 'inactive'
                )
            >
                Inactive
            </option>

        </select>
    </div>

</div>