{{-- ======================================================
     _form: حقول تسجيل الاختبار
     المتغيرات:
       $groupOptions      — قائمة الحلقات
       $assessmentTypes   — أنواع الاختبارات
       $maxScore          — الدرجة القصوى (100)
       $assessment        — للتعديل (اختياري)
====================================================== --}}

<div class="row g-3 mb-3">

    {{-- الحلقة --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الحلقة <span class="text-danger">*</span>
        </label>
        <select id="group_id" name="group_id" class="form-select">
            <option value="">— اختر الحلقة (اختياري) —</option>
            @foreach($groupOptions as $gId => $gName)
                <option value="{{ $gId }}"
                    @selected(old('group_id', $assessment->group_id ?? '') == $gId)>
                    {{ $gName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- نوع الاختبار --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            نوع الاختبار <span class="text-danger">*</span>
        </label>
        <select name="type" class="form-select">
            @foreach($assessmentTypes as $t)
                <option value="{{ $t }}"
                    @selected(old('type', $assessment->type ?? '') === $t)>
                    {{ $t }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- التاريخ --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            تاريخ الاختبار <span class="text-danger">*</span>
        </label>
        <input type="date" name="assessment_date" class="form-control"
               value="{{ old('assessment_date', isset($assessment) ? optional($assessment->assessment_date)->format('Y-m-d') : now()->toDateString()) }}">
    </div>

</div>

{{-- الطالب + المعلم --}}
<div class="row g-3 mb-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الطالب <span class="text-danger">*</span>
        </label>

        <div id="student_loading" class="d-none text-muted small mb-1">
            <span class="spinner-border spinner-border-sm"></span> جاري التحميل…
        </div>

        <select id="student_id" name="student_id" class="form-select">
            <option value="">— اختر الطالب —</option>
            @isset($assessment)
                <option value="{{ $assessment->student_id }}" selected>
                    {{ $assessment->student->full_name }}
                </option>
            @endisset
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">
            المعلم <span class="text-danger">*</span>
        </label>
        @if(count($teacherOptions ?? []) > 0)
            <select name="teacher_id" class="form-select">
                <option value="">— اختر المعلم —</option>
                @foreach($teacherOptions as $tId => $tName)
                    <option value="{{ $tId }}"
                        @selected(old('teacher_id', $assessment->teacher_id ?? auth()->id()) == $tId)>
                        {{ $tName }}
                    </option>
                @endforeach
            </select>
        @else
            <div class="alert alert-warning small mb-0">
                <i class="ti ti-alert-circle me-1"></i>
                لا توجد معلمين متاحين في فرعك
            </div>
            <input type="hidden" name="teacher_id" value="{{ auth()->id() }}">
        @endif
    </div>

</div>

<hr class="my-4">

{{-- النتائج (اختيارية) --}}
<h6 class="fw-semibold mb-3"><i class="ti ti-target me-2 text-primary"></i> نتائج الاختبار</h6>

<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            نتيجة الحفظ (من {{ $maxScore }})
        </label>
        <input type="number" name="memorization_result" class="form-control"
               step="0.5" min="0" max="{{ $maxScore }}"
               placeholder="مثال: 95"
               value="{{ old('memorization_result', $assessment->memorization_result ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            نتيجة التجويد (من {{ $maxScore }})
        </label>
        <input type="number" name="tajweed_result" class="form-control"
               step="0.5" min="0" max="{{ $maxScore }}"
               placeholder="مثال: 88"
               value="{{ old('tajweed_result', $assessment->tajweed_result ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            نتيجة التدبر (من {{ $maxScore }})
        </label>
        <input type="number" name="tadabbur_result" class="form-control"
               step="0.5" min="0" max="{{ $maxScore }}"
               placeholder="مثال: 90"
               value="{{ old('tadabbur_result', $assessment->tadabbur_result ?? '') }}">
    </div>

</div>

{{-- الملاحظات --}}
<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">ملاحظات إضافية</label>
        <textarea name="notes" rows="3" class="form-control"
                  placeholder="سجّل ملاحظاتك حول الاختبار…">{{ old('notes', $assessment->notes ?? '') }}</textarea>
    </div>
</div>

{{-- Ajax: تحميل الطلاب عند تغيير الحلقة --}}
<script>
    (function () {
        const groupSelect   = document.getElementById('group_id');
        const studentSelect = document.getElementById('student_id');
        const loadingEl     = document.getElementById('student_loading');
        const ajaxUrl       = '{{ route('admin.assessments.students-by-group') }}';
        const preSelected   = '{{ old('student_id', $assessment->student_id ?? '') }}';

        function loadStudents(groupId, preserveSelected) {
            if (!groupId) {
                studentSelect.innerHTML = '<option value="">— اختر الطالب —</option>';
                return;
            }

            loadingEl.classList.remove('d-none');
            studentSelect.disabled = true;

            fetch(ajaxUrl + '?group_id=' + groupId, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(students => {
                studentSelect.innerHTML = '<option value="">— اختر الطالب —</option>';
                students.forEach(function (s) {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    if (String(s.id) === String(preserveSelected)) opt.selected = true;
                    studentSelect.appendChild(opt);
                });
            })
            .catch(() => {
                studentSelect.innerHTML = '<option value="">تعذّر تحميل الطلاب</option>';
            })
            .finally(() => {
                loadingEl.classList.add('d-none');
                studentSelect.disabled = false;
            });
        }

        groupSelect.addEventListener('change', function () {
            loadStudents(this.value, '');
        });

        const initialGroup = groupSelect.value;
        if (initialGroup) {
            loadStudents(initialGroup, preSelected);
        }
    })();
</script>

