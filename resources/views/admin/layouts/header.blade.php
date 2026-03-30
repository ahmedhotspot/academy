<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- { Pre-loader } End -->
<!-- { Header } start -->
<header class="site-header">
    <div class="header-wrapper">
        <div class="me-auto flex-grow-1 d-flex align-items-center flex-wrap gap-2">
            <ul class="list-unstyled header-menu-nav">
                <li class="hdr-itm mob-hamburger">
                    <a href="#!" class="app-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger-arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="hdr-itm d-lg-none">
                    <button class="app-head-link border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-header-search" aria-expanded="false" aria-controls="mobile-header-search">
                        <i class="ti ti-search"></i>
                    </button>
                </li>
            </ul>
            <div class="d-none d-md-none d-lg-block header-search ms-3">
                <form action="#">
                    <div class="input-group ">
                        <input class="form-control rounded-3" type="search" value="" id="searchInput" placeholder="Search">
                        <div class="search-btn">
                            <button class="p-0 btn rounded-0 rounded-end" type="button">
                                <i data-feather="search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <nav class="ms-auto header-actions-nav">
            <ul class="header-menu-nav list-unstyled">


                <li class="hdr-itm dropdown user-dropdown ">
                    <a class="app-head-link dropdown-toggle no-caret me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="avtar"><img src="{{asset('dash/assets/images/user/avatar-2.jpg')}}" alt=""></span>
                    </a>
                    <div class="dropdown-menu header-dropdown">
                        <ul class="p-0">

                            <hr class="dropdown-divider">
                            <li class="dropdown-item">
                                <a href="#"
                                   class="drp-link"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i data-feather="log-out"></i>
                                    <span>تسجيل الخروج</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>

                        </ul>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="collapse d-lg-none mobile-header-search w-100" id="mobile-header-search">
            <form action="#">
                <div class="input-group">
                    <input class="form-control rounded-3" type="search" value="" placeholder="Search">
                    <div class="search-btn">
                        <button class="p-0 btn rounded-0 rounded-end" type="button">
                            <i data-feather="search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</header>
