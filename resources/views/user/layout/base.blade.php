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
    <!-- build:css assets/vendor/css/theme.css  -->
    
      
      <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
    
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/css/demo.css') }}" />

    
    <!-- Vendors CSS -->
    
      <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    
    <!-- endbuild -->

    {{-- <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/libs/select2/select2.css') }}
          <link rel="stylesheet" href="../../assets/vendor/libs/plyr/plyr.css" /> --}}

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Page CSS -->
    
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
    @yield('style')

    <!-- Helpers -->
    <script src="{{ asset('themes/sneat/assets/vendor/js/helpers.js') }}"></script>
   
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    
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



<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>


<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>

<!--/ Layout wrapper -->

    <!-- Core JS -->
    
    
    <script src="{{ asset('themes/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    
    <script src="{{ asset('themes/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    {{-- <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script> --}}

      {{-- <script src="../../assets/vendor/libs/pickr/pickr.js"></script> --}}
    
    <script src="{{ asset('themes/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
         
    {{-- <script src="../../assets/vendor/libs/hammer/hammer.js"></script> --}}
        
    {{-- <script src="../../assets/vendor/libs/i18n/i18n.js"></script> --}}
        
      
    <script src="{{ asset('themes/sneat/assets/vendor/js/menu.js') }}"></script>
    
    <!-- endbuild -->

    <!-- Vendors JS -->
    {{-- <script src="../../assets/vendor/libs/select2/select2.js"></script> --}}
    {{-- <script src="../../assets/vendor/libs/plyr/plyr.js"></script> --}}

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main JS -->
    
    <script src="{{ asset('themes/sneat/assets/js/main.js') }}"></script>
    

    <!-- Certificate Checker - Only for USER type -->
    @if(auth()->check() && auth()->user()->user_type === 'USER')
    <script>
    $(document).ready(function() {
        // Check if we should show the certificate popup
        // Only check if user just completed a lesson (session flag) or on first login
        var shouldCheck = sessionStorage.getItem('checkCertificate');
        
        if (shouldCheck === 'true' || shouldCheck === null) {
            $.ajax({
                url: '{{ route("certificate.check") }}',
                type: 'GET',
                success: function(response) {
                    if (response.eligible || (response.has_certificate && !localStorage.getItem('certificate_viewed_' + {{ auth()->id() }}))) {
                        // Show certificate earned popup
                        Swal.fire({
                            icon: 'success',
                            title: '🎉 Congratulations!',
                            html: '<div style="padding: 20px;">' +
                                  '<p style="font-size: 18px; margin: 20px 0; font-weight: 500;">You have successfully completed all lessons!</p>' +
                                  '<div style="background: linear-gradient(135deg, #1E7F5C, #28c76f); color: white; padding: 20px; border-radius: 12px; margin: 20px 0;">' +
                                  '<i class="ri-award-fill" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>' +
                                  '<p style="font-size: 22px; font-weight: 700; margin: 0;">You\'ve Earned Your Certificate!</p>' +
                                  '</div>' +
                                  '<p style="font-size: 14px; color: #666; margin-top: 15px;">View and download your certificate of completion</p>' +
                                  '</div>',
                            confirmButtonText: '<i class="ri-award-line me-2"></i> View My Certificate',
                            confirmButtonColor: '#1E7F5C',
                            showCancelButton: true,
                            cancelButtonText: 'View Later',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'certificate-popup',
                                confirmButton: 'btn-lg',
                                cancelButton: 'btn-lg'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mark as viewed so popup doesn't show again
                                localStorage.setItem('certificate_viewed_' + {{ auth()->id() }}, 'true');
                                window.location.href = '{{ route("certificate.view") }}';
                            } else {
                                // User clicked "View Later" - don't show again this session
                                sessionStorage.setItem('checkCertificate', 'false');
                            }
                        });
                    }
                    
                    // Clear the check flag after first check
                    if (shouldCheck === null) {
                        sessionStorage.setItem('checkCertificate', 'false');
                    }
                },
                error: function(xhr) {
                    console.error('Error checking certificate eligibility');
                }
            });
        }
    });
    </script>
    @endif

    <!-- Page JS -->
    
    @yield('scripts')
    
  </body>
</html>

  <!-- beautify ignore:end -->