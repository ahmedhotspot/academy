<aside class="app-sidebar app-light-sidebar">
    @php
        $user = auth()->user();

        $sidebarSections = [
            [
                'title' => 'الرئيسية',
                'items' => [
                    [
                        'title' => 'لوحة التحكم',
                        'route' => 'admin.dashboard',
                        'route_is' => 'admin.dashboard',
                        'icon' => 'fa fa-chart-pie',
                        'permission' => null,
                    ],
                ],
            ],
            [
                'title' => 'الإدارة الأكاديمية',
                'items' => [
                    ['title' => 'الفروع', 'route' => 'admin.branches.index', 'route_is' => 'admin.branches.*', 'icon' => 'fa fa-building', 'permission' => 'branches.view'],
                    ['title' => 'الطلاب', 'route' => 'admin.students.index', 'route_is' => 'admin.students.*', 'icon' => 'fa fa-user-check', 'permission' => 'students.view'],
                    ['title' => 'أولياء الأمور', 'route' => 'admin.guardians.index', 'route_is' => 'admin.guardians.*', 'icon' => 'fa fa-user', 'permission' => 'guardians.view'],
                    ['title' => 'الحلقات', 'route' => 'admin.groups.index', 'route_is' => 'admin.groups.*', 'icon' => 'fa fa-book', 'permission' => 'groups.view'],
                    ['title' => 'تسجيل في الحلقات', 'route' => 'admin.student-enrollments.index', 'route_is' => 'admin.student-enrollments.*', 'icon' => 'fa fa-users', 'permission' => 'student-enrollments.view'],
                    ['title' => 'المستويات والمسارات', 'route' => 'admin.study-levels.index', 'route_is' => 'admin.study-levels.*', 'icon' => 'fa fa-graduation-cap', 'permission' => 'study-levels.view'],
                ],
            ],
            [
                'title' => 'المتابعة والتقييم',
                'items' => [
                    ['title' => 'حضور المعلمين', 'route' => 'admin.teacher-attendances.index', 'route_is' => 'admin.teacher-attendances.*', 'icon' => 'fa fa-calendar-check', 'permission' => 'teacher-attendances.view'],
                    ['title' => 'المتابعة التعليمية', 'route' => 'admin.student-progress-logs.index', 'route_is' => 'admin.student-progress-logs.*', 'icon' => 'fa fa-chart-line', 'permission' => 'student-progress-logs.view'],
                    ['title' => 'الاختبارات', 'route' => 'admin.assessments.index', 'route_is' => 'admin.assessments.*', 'icon' => 'fa fa-flask', 'permission' => 'assessments.view'],
                ],
            ],
            [
                'title' => 'الشؤون المالية',
                'items' => [
                    ['title' => 'خطط الرسوم', 'route' => 'admin.fee-plans.index', 'route_is' => 'admin.fee-plans.*', 'icon' => 'fa fa-money-bill', 'permission' => 'fee-plans.view'],
                    ['title' => 'اشتراكات الطلاب', 'route' => 'admin.student-subscriptions.index', 'route_is' => 'admin.student-subscriptions.*', 'icon' => 'fa fa-receipt', 'permission' => 'student-subscriptions.view'],
                    ['title' => 'المدفوعات والإيصالات', 'route' => 'admin.payments.index', 'route_is' => 'admin.payments.*', 'icon' => 'fa fa-file-invoice', 'permission' => 'payments.view'],
                    ['title' => 'مصروفات التشغيل', 'route' => 'admin.expenses.index', 'route_is' => 'admin.expenses.*', 'icon' => 'fa fa-coins', 'permission' => 'expenses.view'],
                    ['title' => 'مستحقات المعلمين', 'route' => 'admin.teacher-payrolls.index', 'route_is' => 'admin.teacher-payrolls.*', 'icon' => 'fa fa-wallet', 'permission' => 'teacher-payrolls.view'],
                ],
            ],
            [
                'title' => 'التقارير',
                'items' => [
                    ['title' => 'التقارير والإحصائيات', 'route' => 'admin.reports.index', 'route_is' => 'admin.reports.*', 'icon' => 'fa fa-bar-chart', 'permission' => 'reports.view'],
                ],
            ],
            [
                'title' => 'الإدارة',
                'items' => [
                    ['title' => 'الإشعارات', 'route' => 'admin.notifications.index', 'route_is' => 'admin.notifications.*', 'icon' => 'fa fa-bell', 'permission' => null],
                    ['title' => 'المستخدمون', 'route' => 'admin.users.index', 'route_is' => 'admin.users.*', 'icon' => 'fa fa-users', 'permission' => 'users.view'],
                    ['title' => 'الإعدادات', 'route' => 'admin.settings.index', 'route_is' => 'admin.settings.*', 'icon' => 'fa fa-cog', 'permission' => 'settings.manage'],
                    ['title' => 'الاستيراد والتصدير', 'route' => 'admin.import-export.index', 'route_is' => 'admin.import-export.*', 'icon' => 'fa fa-exchange', 'permission' => 'settings.manage'],
                    ['title' => 'النسخ الاحتياطي', 'route' => 'admin.backup.index', 'route_is' => 'admin.backup.*', 'icon' => 'fa fa-database', 'permission' => 'settings.manage'],
                ],
            ],
        ];
    @endphp

    <div class="app-navbar-wrapper">
        <div class="brand-link brand-logo">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('dash/assets/images/8fb1f1a5-c2d9-4ee6-8f7b-d9bb9615d598.jpg') }}" width="180px" height="45px" alt="أكاديمية القرآن" class="logo logo-lg" />
            </a>
        </div>

        <div class="navbar-content">
            <ul class="app-navbar">
                @foreach($sidebarSections as $section)
                    @php
                        $visibleItems = array_values(array_filter($section['items'], fn ($item) => empty($item['permission']) || $user?->can($item['permission'])));
                    @endphp

                    @if($visibleItems !== [])
                        <li class="nav-item nav-caption"><label>{{ $section['title'] }}</label></li>

                        @foreach($visibleItems as $item)
                            <li class="nav-item">
                                <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['route_is']) ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="{{ $item['icon'] }}"></i></span>
                                    <span class="nav-text">{{ $item['title'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach

            </ul>
        </div>
    </div>
</aside>
