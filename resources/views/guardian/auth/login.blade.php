<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>تسجيل دخول ولي الأمر</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="icon" href="{{ asset('dash/assets/images/favicon.svg') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('dash/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dash/assets/css/style-rtl.css') }}" id="main-style-link">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:slnt,wght@-2,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Cairo", sans-serif; font-weight: 600; }
        .password-toggle { position: absolute; top: 50%; left: 16px; transform: translateY(-50%); cursor: pointer; color: #667085; font-size: 20px; }
        .password-toggle:hover { color: #1e3a5f; }
        .portal-label { background: linear-gradient(135deg,#1e3a5f,#2d6a9f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body>
<div class="m-0 authentication-inner row">
    <div class="p-0 d-none d-lg-block col-lg-7 col-xl-8 img-side">
        <img class="img-fluid w-100" src="{{ asset('dash/assets/images/8fb1f1a5-c2d9-4ee6-8f7b-d9bb9615d598.jpg') }}" alt="">
    </div>

    <div class="p-4 d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5">
        <form class="form w-100" method="POST" action="{{ route('guardian.login') }}">
            @csrf
            <div class="mx-auto w-px-200">
                <div class="mb-3 text-center">
                    <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i class="ti ti-user-heart" style="font-size:2rem;color:#1e3a5f;"></i>
                    </div>
                    <h4 class="fw-bold mb-1 portal-label">بوابة ولي الأمر</h4>
                    <p class="text-muted small">أكاديمية القرآن الكريم</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <i class="ti ti-alert-circle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           autocomplete="off" placeholder="05xxxxxxxx"
                           value="{{ old('phone') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="password"
                               class="form-control pe-5" placeholder="••••••••" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i id="eyeIcon" class="ti ti-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember">تذكرني</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="ti ti-login me-1"></i>تسجيل الدخول
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('dash/assets/js/vendor-all.js') }}"></script>
<script src="{{ asset('dash/assets/js/plugins/bootstrap.min.js') }}"></script>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') { input.type = 'text'; icon.classList.replace('ti-eye','ti-eye-off'); }
        else { input.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
    }
</script>
</body>
</html>

