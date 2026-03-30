@php
    $actions = $actions ?? [];
    $showRefresh = $showRefresh ?? true;
    $refreshTarget = $refreshTarget ?? '';
    $exportUrl = $exportUrl ?? null;
    $searchPlaceholder = $searchPlaceholder ?? 'بحث سريع...';
@endphp

<div class="index-toolbar">
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex flex-wrap gap-2">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" class="btn {{ $action['class'] ?? 'btn-primary' }} btn-sm">
                    @isset($action['icon'])
                        <i class="{{ $action['icon'] }} me-1"></i>
                    @endisset
                    {{ $action['title'] }}
                </a>
            @endforeach

            @if($showRefresh)
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.dispatchEvent(new CustomEvent('admin:index-refresh', { detail: '{{ $refreshTarget }}' }));">
                    <i class="ti ti-refresh me-1"></i>
                    تحديث الجدول
                </button>
            @endif

            @if($exportUrl)
                <a href="{{ $exportUrl }}" class="btn btn-outline-success btn-sm">
                    <i class="ti ti-file-export me-1"></i>
                    تصدير
                </a>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="min-width: 220px;">
                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                <input type="text" id="global-index-search" class="form-control" placeholder="{{ $searchPlaceholder }}">
            </div>
        </div>
    </div>
</div>

