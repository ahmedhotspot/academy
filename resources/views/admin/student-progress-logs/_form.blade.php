{{-- ======================================================
     _form: حقول المتابعة التعليمية اليومية
     يُستخدم في create و edit
     المتغيرات المتاحة:
       $groupOptions        — قائمة الحلقات
       $teacherOptions      — قائمة المعلمين حسب الفرع
       $evaluationLevels    — مستويات التقييم
       $commitmentStatuses  — حالات الالتزام
       $log (اختياري)       — سجل موجود عند التعديل
       $currentStudents     — طلاب الحلقة الحالية (edit فقط)
====================================================== --}}

@php
    $selectedGroupId = old('group_id', $log->group_id ?? ($prefillGroupId ?? ''));
    $selectedStudentId = old('student_id', $log->student_id ?? ($prefillStudentId ?? ''));
@endphp

{{-- ── الصف الأول: الحلقة + التاريخ ── --}}
<div class="row g-3 mb-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الحلقة <span class="text-danger">*</span>
        </label>
        <select id="group_id" name="group_id" class="form-select">
            <option value="">— اختر الحلقة —</option>
            @foreach($groupOptions as $gId => $gName)
                <option value="{{ $gId }}"
                    @selected((string) $selectedGroupId === (string) $gId)>
                    {{ $gName }}
                </option>
            @endforeach
        </select>
        <div id="group_id_feedback" class="invalid-feedback"></div>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">
            تاريخ المتابعة <span class="text-danger">*</span>
        </label>
        <input type="date" name="progress_date" class="form-control"
               value="{{ old('progress_date', isset($log) ? optional($log->progress_date)->format('Y-m-d') : now()->toDateString()) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">
            المعلم <span class="text-danger">*</span>
        </label>
        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
            <option value="">— اختر المعلم —</option>
            @foreach($teacherOptions as $teacherId => $teacherName)
                <option value="{{ $teacherId }}" @selected((string) old('teacher_id', $log->teacher_id ?? '') === (string) $teacherId)>
                    {{ $teacherName }}
                </option>
            @endforeach
        </select>
        @error('teacher_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

{{-- ── الطالب (يُملأ من Ajax بعد اختيار الحلقة) ── --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            الطالب <span class="text-danger">*</span>
        </label>

        <div id="student_loading" class="d-none text-muted small mb-1">
            <span class="spinner-border spinner-border-sm"></span> جاري تحميل الطلاب…
        </div>

        <select id="student_id" name="student_id" class="form-select">
            <option value="">— اختر الطالب —</option>
            @if(!empty($selectedStudentId) && isset($log))
                <option value="{{ $selectedStudentId }}" selected>
                    {{ $log->student->full_name }}
                </option>
            @endif
        </select>
    </div>
</div>

<hr class="my-3">

{{-- ── الحفظ والمراجعة ── --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            مقدار الحفظ <span class="text-danger">*</span>
        </label>
        <input type="text" name="memorization_amount" class="form-control"
               placeholder="مثال: ربع صفحة، سورة الفاتحة…"
               value="{{ old('memorization_amount', $log->memorization_amount ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            مقدار المراجعة <span class="text-danger">*</span>
        </label>
        <input type="text" name="revision_amount" class="form-control"
               placeholder="مثال: صفحتان، الجزء الأول…"
               value="{{ old('revision_amount', $log->revision_amount ?? '') }}">
    </div>
</div>

{{-- ── التقييمات الثلاثة ── --}}
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            تقييم التجويد <span class="text-danger">*</span>
        </label>
        <select name="tajweed_evaluation" class="form-select">
            @foreach($evaluationLevels as $level)
                <option value="{{ $level }}"
                    @selected(old('tajweed_evaluation', $log->tajweed_evaluation ?? '') === $level)>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            تقييم التدبر <span class="text-danger">*</span>
        </label>
        <select name="tadabbur_evaluation" class="form-select">
            @foreach($evaluationLevels as $level)
                <option value="{{ $level }}"
                    @selected(old('tadabbur_evaluation', $log->tadabbur_evaluation ?? '') === $level)>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            مستوى الإتقان <span class="text-danger">*</span>
        </label>
        <select name="mastery_level" class="form-select">
            @foreach($evaluationLevels as $level)
                <option value="{{ $level }}"
                    @selected(old('mastery_level', $log->mastery_level ?? '') === $level)>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

</div>

{{-- ── الالتزام + الأخطاء ── --}}
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            حالة الالتزام <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-3 mt-1">
            @foreach($commitmentStatuses as $cs)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                           name="commitment_status" id="cs_{{ $loop->index }}"
                           value="{{ $cs }}"
                           @checked(old('commitment_status', $log->commitment_status ?? 'ملتزم') === $cs)>
                    <label class="form-check-label" for="cs_{{ $loop->index }}">{{ $cs }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-8">
        <label class="form-label fw-semibold">الأخطاء المتكررة</label>
        <textarea name="repeated_mistakes" rows="2" class="form-control"
                  placeholder="سجّل الأخطاء المتكررة للطالب إن وجدت…">{{ old('repeated_mistakes', $log->repeated_mistakes ?? '') }}</textarea>
    </div>

</div>

{{-- ── الملاحظات ─ـ --}}
<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">ملاحظات إضافية</label>
        <textarea name="notes" rows="3" class="form-control"
                  placeholder="أي ملاحظات تخص هذه الجلسة…">{{ old('notes', $log->notes ?? '') }}</textarea>
    </div>
</div>

{{-- ── Ajax: تحميل الطلاب عند تغيير الحلقة ── --}}
<script>
    (function () {
        const groupSelect   = document.getElementById('group_id');
        const studentSelect = document.getElementById('student_id');
        const loadingEl     = document.getElementById('student_loading');
        const ajaxUrl       = '{{ route('admin.student-progress-logs.students-by-group') }}';
        const preSelected   = '{{ $selectedStudentId }}';

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

        // عند تحميل الصفحة: إذا كانت الحلقة محددة مسبقًا (تعديل أو بعد validation فشل)
        const initialGroup = groupSelect.value;
        if (initialGroup) {
            loadStudents(initialGroup, preSelected);
        }
    })();
</script>

