<!Doctype html>

<html
  lang="en"
  class="layout-navbar-fixed layout-wide "
  data-assets-path="{{ asset('themes/sneat/assets') }}"
  data-template="horizontal-menu-template">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

      <title>@yield('title')</title>
     
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo/logo-white-bg.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
    
    <style>
        /* Certificate Notification Badge */
        .certificate-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            animation: slideInRight 0.5s ease-out, pulse 2s ease-in-out 2s infinite;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .certificate-badge {
            background: linear-gradient(135deg, #1E7F5C 0%, #28c76f 100%);
            color: white;
            padding: 18px 24px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(30, 127, 92, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            min-width: 260px;
        }

        .certificate-badge:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(30, 127, 92, 0.5);
            color: white;
        }

        .certificate-badge-icon {
            font-size: 32px;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .certificate-badge-content {
            flex: 1;
        }

        .certificate-badge-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .certificate-badge-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
        }

        .certificate-badge-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 16px;
            line-height: 1;
        }

        .certificate-badge-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .certificate-notification.hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .certificate-notification {
                bottom: 20px;
                right: 20px;
                left: 20px;
            }
            
            .certificate-badge {
                min-width: auto;
                width: 100%;
            }
        }

        @media print {
            .certificate-notification {
                display: none !important;
            }
        }
    </style>
    
    @yield('style')

    <!-- Helpers -->
    <script src="{{ asset('themes/sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/js/config.js') }}"></script>
  </head>

  <body>
        
    <!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
  <div class="layout-container">
    
<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
  <div class="container-xxl">

  <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
    <a href="{{ route('user.home') }}" class="app-brand-link">
      <span class="">
        <img src="{{ asset('img/landing/logo.png') }}" alt="" style="width: 40px; height: auto; max-width: 100%;">
      </span>
      <span class="app-brand-text demo text-dark menu-text fw-bold ms-2 ps-1">CyberWais</span>
    </a>
  </div>

  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0  d-xl-none  ">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <li class="nav-item lh-1 me-4 text-end">
          <h6 class="mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
          <small class="text-body-secondary">{{ auth()->user()->user_type }}</small>
      </li>

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('img/profile/default.png') }}" alt class="rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="pages-account-settings-account.html">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ asset('img/profile/default.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
                  <small class="text-body-secondary">{{ auth()->user()->user_type }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          @if(auth()->user()->certificate)
          <li>
            <a class="dropdown-item" href="{{ route('certificate.view') }}">
              <i class="icon-base bx bx-award icon-md me-3"></i><span>My Certificate</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          @endif
          <li>
            <a class="dropdown-item" href="pages-faq.html"> <i class="icon-base bx bx-help-circle icon-md me-3"></i><span>FAQ</span> </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('auth.logout') }}"> <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span> </a>
          </li>
        </ul>
      </li>
      <!--/ User -->
  </ul>
</div>
</div>
</nav>
<!-- / Navbar -->

    <!-- Layout container -->
    <div class="layout-page">
      <!-- Content wrapper -->
      <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
          @yield('content')
        </div>
        <!--/ Content -->        

        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
          <div class="container-xxl">
            <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , made with ❤️
              </div>
            </div>
          </div>
        </footer>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
      </div>
      <!--/ Content wrapper -->
    </div>
    <!--/ Layout container -->
  </div>
</div>

<!-- Certificate Notification Badge (Only for USER type with certificate) -->
@if(auth()->check() && auth()->user()->user_type === 'USER' && auth()->user()->certificate && !request()->routeIs('certificate.view'))
<div class="certificate-notification" id="certificateNotification">
    <a href="{{ route('certificate.view') }}" class="certificate-badge">
        <div class="certificate-badge-icon">🏆</div>
        <div class="certificate-badge-content">
            <div class="certificate-badge-title">CERTIFICATE EARNED!</div>
            <div class="certificate-badge-subtitle">Click to view your achievement</div>
        </div>
        <button type="button" class="certificate-badge-close" onclick="event.preventDefault(); event.stopPropagation(); hideCertificateNotification();">
            ×
        </button>
    </a>
</div>
@endif

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>

    <!-- Core JS -->
    <script src="{{ asset('themes/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main JS -->
    <script src="{{ asset('themes/sneat/assets/js/main.js') }}"></script>

    <!-- Certificate Notification Script -->
    <script>
    function hideCertificateNotification() {
        const notification = document.getElementById('certificateNotification');
        if (notification) {
            notification.classList.add('hidden');
            // Store in localStorage so it stays hidden
            localStorage.setItem('certificateNotificationHidden_{{ auth()->id() }}', 'true');
        }
    }

    // Check if notification was previously hidden
    $(document).ready(function() {
        const notificationHidden = localStorage.getItem('certificateNotificationHidden_{{ auth()->id() }}');
        if (notificationHidden === 'true') {
            const notification = document.getElementById('certificateNotification');
            if (notification) {
                notification.classList.add('hidden');
            }
        }
    });
    </script>

    <!-- Certificate Checker - Only for USER type -->
    @if(auth()->check() && auth()->user()->user_type === 'USER')
    <script>
    $(document).ready(function() {
        const userId = '{{ auth()->id() }}';

        function ensureCertificateNotification() {
            if (document.getElementById('certificateNotification')) {
                return;
            }

            const notification = document.createElement('div');
            notification.className = 'certificate-notification';
            notification.id = 'certificateNotification';
            notification.innerHTML = `
                <a href="{{ route('certificate.view') }}" class="certificate-badge">
                    <div class="certificate-badge-icon">🏆</div>
                    <div class="certificate-badge-content">
                        <div class="certificate-badge-title">CERTIFICATE EARNED!</div>
                        <div class="certificate-badge-subtitle">Click to view your achievement</div>
                    </div>
                    <button type="button" class="certificate-badge-close">×</button>
                </a>
            `;

            document.body.appendChild(notification);

            const closeBtn = notification.querySelector('.certificate-badge-close');
            closeBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                hideCertificateNotification();
            });

            const notificationHidden = localStorage.getItem(`certificateNotificationHidden_${userId}`);
            if (notificationHidden === 'true') {
                notification.classList.add('hidden');
            }
        }

        $.ajax({
            url: '{{ route("certificate.check") }}',
            type: 'GET',
            success: function(response) {
                if (response.just_earned_certificate && response.certificate) {
                    const celebrationKey = `certificateCelebrated_${userId}_${response.certificate.certificate_number}`;

                    ensureCertificateNotification();

                    if (!localStorage.getItem(celebrationKey)) {
                        localStorage.setItem(celebrationKey, 'true');

                        const emailMessage = response.email_sent
                            ? `<p style="font-size: 14px; color: #1E7F5C; margin-top: 15px;">${response.email_message}</p>`
                            : '<p style="font-size: 14px; color: #666; margin-top: 15px;">Your certificate is ready now. You can view it here and resend the email anytime.</p>';

                        Swal.fire({
                            icon: 'success',
                            title: 'Congratulations!',
                            html: '<div style="padding: 20px;">' +
                                  '<p style="font-size: 18px; margin: 20px 0; font-weight: 500;">You have successfully completed all lessons and passed the post-assessment.</p>' +
                                  '<div style="background: linear-gradient(135deg, #1E7F5C, #28c76f); color: white; padding: 20px; border-radius: 12px; margin: 20px 0;">' +
                                  '<i class="ri-award-fill" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>' +
                                  '<p style="font-size: 22px; font-weight: 700;">Your certificate is ready!</p>' +
                                  '</div>' +
                                  emailMessage +
                                  '</div>',
                            confirmButtonText: '<i class="ri-award-line me-2"></i> View My Certificate',
                            confirmButtonColor: '#1E7F5C',
                            showCancelButton: true,
                            cancelButtonText: 'Later',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("certificate.view") }}';
                            }
                        });
                    }

                    return;
                }

                if (response.post_assessment_ready && response.section) {
                    const promptKey = `postAssessmentPromptSeen_${userId}_${response.section.id}`;

                    if (!localStorage.getItem(promptKey) && !window.location.pathname.includes('/assessment/post/')) {
                        localStorage.setItem(promptKey, 'true');

                        Swal.fire({
                            icon: 'info',
                            title: 'All Lessons Completed',
                            html: `<div style="padding: 10px 0;">
                                <p style="font-size: 16px; margin-bottom: 12px;">
                                    Congratulations, you have completed all lessons for <strong>${response.section.name}</strong>.
                                </p>
                                <p style="font-size: 14px; color: #666; margin-bottom: 0;">
                                    Take the post-assessment now to claim your certificate.
                                </p>
                            </div>`,
                            confirmButtonText: '<i class="ri-file-list-3-line me-2"></i> Take Post-Assessment',
                            confirmButtonColor: '#696cff',
                            showCancelButton: true,
                            cancelButtonText: 'Later',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = response.post_assessment_url;
                            }
                        });
                    }
                }
            },
            error: function() {
                console.error('Error checking certificate eligibility');
            }
        });
    });
    </script>
    @endif

    <!-- Page JS -->
    @yield('scripts')
    
  </body>
</html>
