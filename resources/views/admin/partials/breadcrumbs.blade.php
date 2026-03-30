{{-- ====================================================
     Partial: Breadcrumbs موحّدة
     الاستخدام:
       @include('admin.partials.breadcrumbs', [
           'breadcrumbs' => [
               ['title' => 'لوحة التحكم', 'url' => route('admin.dashboard')],
               ['title' => 'الطلاب',      'url' => route('admin.students.index')],
               ['title' => 'إضافة طالب'], // آخر عنصر بدون url
           ]
       ])
     ==================================================== --}}

@isset($breadcrumbs)
    <nav aria-label="مسار التنقل">
        <ol class="breadcrumb mb-0">
            @foreach($breadcrumbs as $crumb)
                @if(!$loop->last)
                    <li class="breadcrumb-item">
                        @if(!empty($crumb['url']))
                            <a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a>
                        @else
                            <span>{{ $crumb['title'] }}</span>
                        @endif
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $crumb['title'] }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endisset

