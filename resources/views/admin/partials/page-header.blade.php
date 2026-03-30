{{-- ====================================================
     Partial: Page Header الموحّد لجميع الصفحات
     يشمل: عنوان الصفحة + وصف مختصر + breadcrumbs + شريط أدوات
     ==================================================== --}}

@php
    $resolvedTitle = $title ?? $pageTitle ?? '';
    $description = $description ?? 'إدارة البيانات، عرض السجلات، والبحث والإجراءات المرتبطة بها.';
    $actions = $actions ?? [];
    $routeName = request()->route()?->getName() ?? '';
    $isIndexRoute = str_ends_with($routeName, '.index');
    $showToolbar = ($showToolbar ?? false) || $isIndexRoute || count($actions) > 0;
@endphp

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $resolvedTitle }}</h5>

                </div>
                @include('admin.partials.breadcrumbs')
            </div>
        </div>
    </div>
</div>

@if($showToolbar)
    <div class="index-toolbar">
        <div class="d-flex gap-2 justify-content-between flex-wrap align-items-center">
            <div class="d-flex gap-2 flex-wrap">
                @foreach($actions as $action)
                    <a href="{{ $action['url'] }}" class="btn {{ $action['class'] ?? 'btn-primary' }} btn-sm">
                        @isset($action['icon'])
                            <i class="{{ $action['icon'] }} me-1"></i>
                        @endisset
                        {{ $action['title'] }}
                    </a>
                @endforeach

                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.location.reload()">
                    <i class="ti ti-refresh me-1"></i>
                    تحديث
                </button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="min-width:220px;">
                    <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                    <input type="text" id="global-index-search" class="form-control" placeholder="بحث داخل الصفحة...">
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('global-index-search');
            if (!input) return;
            input.addEventListener('input', function () {
                const dtFilter = document.querySelector('.dataTables_filter input');
                if (dtFilter) {
                    dtFilter.value = this.value;
                    dtFilter.dispatchEvent(new Event('keyup'));
                    return;
                }
                // fallback: بحث نصي بسيط داخل الصفحة لصفحات غير DataTable
                const q = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('table tbody tr');
                rows.forEach(function (row) {
                    row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
@endif
