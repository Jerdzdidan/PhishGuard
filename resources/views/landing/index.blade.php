<!DOCTYPE html>

<html
  lang="en"
  class=" layout-navbar-fixed layout-wide "
  data-assets-path="{{ asset('themes/sneat/assets') }}"
  data-template="front-pages">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

    <title>PhishGuard</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo/logo-white-bg.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/fonts/iconify-icons.css') }}" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/css/demo.css') }}" />



    <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/front-page.css') }}" />
    
    <!-- Vendors CSS -->
    
    <!-- endbuild -->

    <!-- Page CSS -->
    
  <link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/front-page-landing.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('themes/sneat/assets/vendor/js/helpers.js') }}"></script>
    
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    
    <script src="{{ asset('themes/sneat/assets/js/config.js') }}"></script>
    
  </head>

  <body>
    

<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8">
        <!-- Mobile menu toggle: Start-->
        <button class="navbar-toggler border-0 px-0 me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base bx bx-menu icon-lg align-middle text-heading fw-medium"></i>
        </button>
        <!-- Mobile menu toggle: End-->
        <a href="{{ route('landing.index') }}" class="app-brand-link">
          <span class="">
            <img src="{{ asset('img/landing/logo.png') }}" alt="" style="width: 40px; height: auto; max-width: 100%;">
          </span>
          <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1">PhishGuard</span>
        </a>
      </div>
      <!-- Menu logo wrapper: End -->
      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base bx bx-x icon-lg"></i>
        </button>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link fw-medium active" aria-current="page" href="{{ route('landing.index') }}">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="">About Us</a>
          </li>
        </ul>
      </div>
      <div class="landing-menu-overlay d-lg-none"></div>
      <!-- Menu wrapper: End -->
      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        
        <!-- navbar button: Start -->
        <li class="ms-2">
          <a href="{{ route('auth.sign-in') }}" class="btn btn-outline-primary"><span class="d-none d-md-block">Sign in</span></a>
        </li>
        <li class="ms-2">
          <a href="{{ route('auth.sign-up') }}" class="btn btn-primary"><span class="d-none d-md-block">Sign up</span></a>
        </li>
        <!-- navbar button: End -->
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
<!-- Navbar: End -->


<!-- Sections:Start -->

  <div data-bs-spy="scroll" class="scrollspy-example">
    <!-- Hero: Start -->
    <section id="hero-animation">
      <div id="landingHero" class="section-py landing-hero position-relative">
        <img src="{{ asset('img/landing/backgrounds/hero-bg.png') }}" alt="hero background" class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100" data-speed="1" />
        <div class="container">
          <div class="hero-text-box text-center position-relative">
            <h1 class="text-primary hero-title display-6 fw-extrabold">"Learn" to Protect Yourself from Cyber Threats</h1>
            <h2 class="hero-sub-title h6 mb-6">
              Comprehensive security awareness training<br class="d-none d-lg-block" />
              to educate and empower everyone.
            </h2>
            <div class="landing-hero-btn d-inline-block position-relative">
              <span class="hero-btn-item position-absolute d-none d-md-flex fw-medium">Learn now! <img src="{{ asset('img/landing/icons/Join-community-arrow.png') }}" alt="Join community arrow" class="scaleX-n1-rtl" /></span>
              <a href="{{ route('auth.sign-in') }}" class="btn btn-primary btn-lg mx-4">Sign in</a>
            </div>
          </div>
          <div id="heroDashboardAnimation" class="hero-animation-img">
            <a href="{{ route('auth.sign-in') }}">
              <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
                <img src="{{ asset('img/landing/page/hero-main.png') }}" alt="hero dashboard" class="animation-img" data-app-light-img="front-pages/landing-page/hero-dashboard-light.png" data-app-dark-img="front-pages/landing-page/hero-dashboard-dark.png" />
                <img src="{{ asset('img/landing/page/hero-elements.png') }}" alt="hero elements" class="position-absolute hero-elements-img animation-img top-0 start-0" data-app-light-img="{{ asset('img/landing/page/hero-elements-light.png') }}" />
              </div>
            </a>
          </div>
        </div>
      </div>
      <div class="landing-hero-blank"></div>
    </section>
    <!-- Hero: End -->

    <!-- Useful features: Start -->
    <section id="landingFeatures" class="section-py landing-features border-top border-bottom">
      <div class="container">
        <div class="text-center mb-4">
          <span class="badge bg-label-primary">Platform Features</span>
        </div>
        <h4 class="text-center mb-1">
          <span class="position-relative fw-extrabold z-1"
            >Comprehensive Cybersecurity
            <img src="{{ asset('img/landing/icons/section-title-icon.png') }}" alt="security icon" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
          </span>
          Learning & Training
        </h4>
        <p class="text-center mb-12">Master cybersecurity awareness with interactive lessons, phishing simulations, and real-world threat scenarios.</p>
        <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12">
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-clock fa-3x"></i>
            </div>
            <h5 class="mb-2">Real-Time Learning</h5>
            <p class="features-icon-description">Learn at your own pace with engaging, self-paced courses designed for all skill levels.</p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-shield-alt fa-3x"></i>
            </div>
            <h5 class="mb-2">Threat Detection</h5>
            <p class="features-icon-description">Learn to identify phishing emails, malware, and social engineering attacks before they strike.</p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-lock fa-3x"></i>
            </div>
            <h5 class="mb-2">Security Awareness</h5>
            <p class="features-icon-description">Build a strong security culture through interactive training modules and best practices.</p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-comments fa-3x"></i>
            </div>
            <h5 class="mb-2">Instant Feedback</h5>
            <p class="features-icon-description">Get immediate feedback on your performance with detailed explanations and corrections.</p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-chart-line fa-3x"></i>
            </div>
            <h5 class="mb-2">Progress Tracking</h5>
            <p class="features-icon-description">Monitor your learning progress with detailed analytics and achievement badges.</p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <i class="fas fa-certificate fa-3x"></i>
            </div>
            <h5 class="mb-2">Certifications</h5>
            <p class="features-icon-description">Earn certificates upon course completion to validate your cybersecurity knowledge.</p>
          </div>
        </div>
      </div>
    </section>
    <!-- Useful features: End -->

    <!-- Fun facts: Start -->
    <section id="landingFunFacts" class="section-py landing-fun-facts border-top">
      <div class="container">
        <div class="row gy-6">
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-primary shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-primary">
                  <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2" d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663H10Z" fill="currentColor" />
                    <path
                      d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663M36 22.4663H28M6 44.4663H58V48.4663C58 49.5272 57.5786 50.5446 56.8284 51.2947C56.0783 52.0449 55.0609 52.4663 54 52.4663H10C8.93913 52.4663 7.92172 52.0449 7.17157 51.2947C6.42143 50.5446 6 49.5272 6 48.4663V44.4663Z"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">7.1k+</h3>
                <p class="fw-medium mb-0">
                  Support Tickets<br />
                  Resolved
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-success shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-success">
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="User">
                      <path
                        id="Vector"
                        opacity="0.2"
                        d="M32.4999 8.52881C27.6437 8.52739 22.9012 9.99922 18.899 12.7499C14.8969 15.5005 11.8233 19.4006 10.0844 23.9348C8.34542 28.4691 8.02291 33.4242 9.15945 38.1456C10.296 42.867 12.8381 47.1326 16.4499 50.3788C17.9549 47.4151 20.2511 44.9261 23.0841 43.1875C25.917 41.4489 29.176 40.5287 32.4999 40.5288C30.5221 40.5288 28.5887 39.9423 26.9442 38.8435C25.2997 37.7447 24.018 36.1829 23.2611 34.3556C22.5043 32.5284 22.3062 30.5177 22.6921 28.5779C23.0779 26.6381 24.0303 24.8563 25.4289 23.4577C26.8274 22.0592 28.6092 21.1068 30.549 20.721C32.4888 20.3351 34.4995 20.5331 36.3268 21.29C38.154 22.0469 39.7158 23.3286 40.8146 24.9731C41.9135 26.6176 42.4999 28.551 42.4999 30.5288C42.4999 33.181 41.4464 35.7245 39.571 37.5999C37.6956 39.4752 35.1521 40.5288 32.4999 40.5288C35.8238 40.5287 39.0829 41.4489 41.9158 43.1875C44.7487 44.9261 47.045 47.4151 48.5499 50.3788C52.1618 47.1326 54.7039 42.867 55.8404 38.1456C56.977 33.4242 56.6545 28.4691 54.9155 23.9348C53.1766 19.4006 50.103 15.5005 46.1008 12.7499C42.0987 9.99922 37.3562 8.52739 32.4999 8.52881Z"
                        fill="currentColor" />
                      <path
                        id="Vector_2"
                        d="M32.5 40.5288C38.0228 40.5288 42.5 36.0517 42.5 30.5288C42.5 25.006 38.0228 20.5288 32.5 20.5288C26.9772 20.5288 22.5 25.006 22.5 30.5288C22.5 36.0517 26.9772 40.5288 32.5 40.5288ZM32.5 40.5288C29.1759 40.5288 25.9168 41.4477 23.0839 43.1866C20.2509 44.9255 17.9548 47.4149 16.45 50.3788M32.5 40.5288C35.8241 40.5288 39.0832 41.4477 41.9161 43.1866C44.7491 44.9255 47.0452 47.4149 48.55 50.3788M56.5 32.5288C56.5 45.7836 45.7548 56.5288 32.5 56.5288C19.2452 56.5288 8.5 45.7836 8.5 32.5288C8.5 19.274 19.2452 8.52881 32.5 8.52881C45.7548 8.52881 56.5 19.274 56.5 32.5288Z"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                    </g>
                  </svg>
                </div>
                <h3 class="mb-0">50k+</h3>
                <p class="fw-medium mb-0">
                  Join Creatives<br />
                  Community
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-info shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-info">
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2" d="M46.5001 10.5288H32.5001L20.2251 26.5288L32.5001 56.5288L60.5001 26.5288L46.5001 10.5288Z" fill="currentColor" />
                    <path d="M18.5 10.5288H46.5L60.5 26.5288L32.5 56.5288L4.5 26.5288L18.5 10.5288Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M33.2934 9.92012C33.1042 9.67343 32.8109 9.52881 32.5 9.52881C32.1891 9.52881 31.8958 9.67343 31.7066 9.92012L19.7318 25.5288H4.5C3.94772 25.5288 3.5 25.9765 3.5 26.5288C3.5 27.0811 3.94772 27.5288 4.5 27.5288H19.5537L31.5745 56.9075C31.7282 57.2833 32.094 57.5288 32.5 57.5288C32.906 57.5288 33.2718 57.2833 33.4255 56.9075L45.4463 27.5288H60.5C61.0523 27.5288 61.5 27.0811 61.5 26.5288C61.5 25.9765 61.0523 25.5288 60.5 25.5288H45.2682L33.2934 9.92012ZM42.7474 25.5288L32.5 12.1717L22.2526 25.5288H42.7474ZM21.7146 27.5288L32.5 53.8881L43.2854 27.5288H21.7146Z"
                      fill="currentColor" />
                  </svg>
                </div>
                <h3 class="mb-0">4.8/5</h3>
                <p class="fw-medium mb-0">
                  Highly Rated<br />
                  Products
                </p>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-warning shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-warning">
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      opacity="0.2"
                      d="M14.125 50.9038C11.825 48.6038 13.35 43.7788 12.175 40.9538C11 38.1288 6.5 35.6538 6.5 32.5288C6.5 29.4038 10.95 27.0288 12.175 24.1038C13.4 21.1788 11.825 16.4538 14.125 14.1538C16.425 11.8538 21.25 13.3788 24.075 12.2038C26.9 11.0288 29.375 6.52881 32.5 6.52881C35.625 6.52881 38 10.9788 40.925 12.2038C43.85 13.4288 48.575 11.8538 50.875 14.1538C53.175 16.4538 51.65 21.2788 52.825 24.1038C54 26.9288 58.5 29.4038 58.5 32.5288C58.5 35.6538 54.05 38.0288 52.825 40.9538C51.6 43.8788 53.175 48.6038 50.875 50.9038C48.575 53.2038 43.75 51.6788 40.925 52.8538C38.1 54.0288 35.625 58.5288 32.5 58.5288C29.375 58.5288 27 54.0788 24.075 52.8538C21.15 51.6288 16.425 53.2038 14.125 50.9038Z"
                      fill="currentColor" />
                    <path
                      d="M43.5 26.5288L28.825 40.5288L21.5 33.5288M14.125 50.9038C11.825 48.6038 13.35 43.7788 12.175 40.9538C11 38.1288 6.5 35.6538 6.5 32.5288C6.5 29.4038 10.95 27.0288 12.175 24.1038C13.4 21.1788 11.825 16.4538 14.125 14.1538C16.425 11.8538 21.25 13.3788 24.075 12.2038C26.9 11.0288 29.375 6.52881 32.5 6.52881C35.625 6.52881 38 10.9788 40.925 12.2038C43.85 13.4288 48.575 11.8538 50.875 14.1538C53.175 16.4538 51.65 21.2788 52.825 24.1038C54 26.9288 58.5 29.4038 58.5 32.5288C58.5 35.6538 54.05 38.0288 52.825 40.9538C51.6 43.8788 53.175 48.6038 50.875 50.9038C48.575 53.2038 43.75 51.6788 40.925 52.8538C38.1 54.0288 35.625 58.5288 32.5 58.5288C29.375 58.5288 27 54.0788 24.075 52.8538C21.15 51.6288 16.425 53.2038 14.125 50.9038Z"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">100%</h3>
                <p class="fw-medium mb-0">
                  Money Back<br />
                  Guarantee
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Fun facts: End -->

    <!-- FAQ: Start -->
<section id="landingFAQ" class="section-py bg-body landing-faq">
  <div class="container">
    <div class="text-center mb-4">
      <span class="badge bg-label-primary">FAQ</span>
    </div>
    <h4 class="text-center mb-1">
      Frequently asked
      <span class="position-relative fw-extrabold z-1"
        >questions
        <img src="{{ asset('img/landing/icons/section-title-icon.png') }}" alt="section icon" class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
      </span>
    </h4>
    <p class="text-center mb-12 pb-md-4">
      Browse through these FAQs to learn more about PhishGuard and how our cybersecurity learning platform works.
    </p>

    <div class="row gy-12 align-items-center">
      <div class="col-lg-5">
        <div class="text-center">
          <img src="{{ asset('img/landing/page/faq-boy-with-logos.png') }}" alt="faq illustration" class="faq-image" />
        </div>
      </div>

      <div class="col-lg-7">
        <div class="accordion" id="accordionExample">

          <!-- FAQ 1 -->
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionOne">
                What cybersecurity topics are covered in PhishGuard?
              </button>
            </h2>
            <div id="accordionOne" class="accordion-collapse collapse">
              <div class="accordion-body">
                PhishGuard covers essential and practical cybersecurity topics such as phishing detection and prevention, email security, password hygiene and multi-factor authentication (MFA), social engineering awareness, malware basics, safe internet browsing, data privacy, and incident response fundamentals.  
                Our content is designed for beginners, students, employees, and aspiring cybersecurity professionals through interactive lessons, simulations, quizzes, and real-world scenarios.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingTwo">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionTwo">
                How long does it take to complete a course?
              </button>
            </h2>
            <div id="accordionTwo" class="accordion-collapse collapse">
              <div class="accordion-body">
                Course duration varies depending on the topic. Short awareness modules can be completed in 20–30 minutes, while full learning paths or certification-style tracks may take 4–8 weeks.  
                PhishGuard is fully self-paced, allowing learners to study anytime and progress based on their own schedule.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="card accordion-item active">
            <h2 class="accordion-header" id="headingThree">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionThree">
                Who is PhishGuard for?
              </button>
            </h2>
            <div id="accordionThree" class="accordion-collapse collapse">
              <div class="accordion-body">
                PhishGuard is designed for students, professionals, educators, and organizations that want to improve cybersecurity awareness and defensive skills. Whether you are a beginner learning online safety or an organization training staff against phishing attacks, PhishGuard adapts to different skill levels and learning goals.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingFour">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionFour">
                Do I need prior cybersecurity experience?
              </button>
            </h2>
            <div id="accordionFour" class="accordion-collapse collapse">
              <div class="accordion-body">
                No prior cybersecurity knowledge is required. PhishGuard starts with fundamentals and gradually introduces more advanced concepts. Each course clearly indicates its difficulty level so learners can choose content appropriate to their background.
              </div>
            </div>
          </div>

          <!-- FAQ 5 -->
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingFive">
              <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#accordionFive">
                Can I access PhishGuard on any device?
              </button>
            </h2>
            <div id="accordionFive" class="accordion-collapse collapse">
              <div class="accordion-body">
                Yes. PhishGuard is a web-based learning platform that works on desktops, laptops, tablets, and mobile devices. All you need is a modern browser and an internet connection to access courses, quizzes, and interactive labs.
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
<!-- FAQ: End -->


<!-- / Sections:End -->



<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text">
 
  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text"
          >©
          <script>
            document.write(new Date().getFullYear());
          </script>
        </span>
        <a href="{{ route('landing.index') }}" class="text-white">PhishGuard,</a>
        <span class="footer-bottom-text"> Made with passion for learning.</span>
      </div>
      <div>
        <a href="#" class="me-4 text-white" >
          <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M10.7184 2.19556C6.12757 2.19556 2.40674 5.91639 2.40674 10.5072C2.40674 14.1789 4.78757 17.2947 8.0909 18.3947C8.50674 18.4697 8.65674 18.2139 8.65674 17.9939C8.65674 17.7964 8.65007 17.2731 8.64757 16.5806C6.33507 17.0822 5.84674 15.4656 5.84674 15.4656C5.47007 14.5056 4.92424 14.2497 4.92424 14.2497C4.17007 13.7339 4.98174 13.7456 4.98174 13.7456C5.81674 13.8039 6.25424 14.6022 6.25424 14.6022C6.9959 15.8722 8.2009 15.5056 8.67257 15.2931C8.7484 14.7556 8.96507 14.3889 9.20174 14.1814C7.35674 13.9722 5.41674 13.2589 5.41674 10.0731C5.41674 9.16722 5.74091 8.42389 6.27007 7.84389C6.1859 7.63306 5.89841 6.78722 6.35257 5.64389C6.35257 5.64389 7.05007 5.41972 8.63757 6.49472C9.31557 6.31028 10.0149 6.21614 10.7176 6.21472C11.4202 6.21586 12.1196 6.31001 12.7976 6.49472C14.3859 5.41889 15.0826 5.64389 15.0826 5.64389C15.5367 6.78722 15.2517 7.63306 15.1651 7.84389C15.6984 8.42389 16.0184 9.16639 16.0184 10.0731C16.0184 13.2672 14.0767 13.9689 12.2251 14.1747C12.5209 14.4314 12.7876 14.9381 12.7876 15.7131C12.7876 16.8247 12.7776 17.7214 12.7776 17.9939C12.7776 18.2164 12.9259 18.4747 13.3501 18.3931C16.6517 17.2914 19.0301 14.1781 19.0301 10.5072C19.0301 5.91639 15.3092 2.19556 10.7184 2.19556Z"
              fill="currentColor" />
          </svg>
        </a>
        <a href="#" class="me-4 text-white" >
          <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.8609 18.0262V11.1962H14.1651L14.5076 8.52204H11.8609V6.81871C11.8609 6.04704 12.0759 5.51871 13.1834 5.51871H14.5868V3.13454C13.904 3.06136 13.2176 3.02603 12.5309 3.02871C10.4943 3.02871 9.09593 4.27204 9.09593 6.55454V8.51704H6.80676V11.1912H9.10093V18.0262H11.8609Z" fill="currentColor" />
          </svg>
        </a>
        <a href="#" class="me-4 text-white" >
          <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.0576 7.19293C17.0684 7.33876 17.0684 7.48376 17.0684 7.62876C17.0684 12.0663 13.6909 17.1796 7.5184 17.1796C5.61674 17.1796 3.85007 16.6288 2.3634 15.6721C2.6334 15.7029 2.8934 15.7138 3.17424 15.7138C4.68506 15.7174 6.15311 15.2122 7.34174 14.2796C6.64125 14.2669 5.96222 14.0358 5.39943 13.6185C4.83665 13.2013 4.41822 12.6187 4.20257 11.9521C4.41007 11.9829 4.6184 12.0038 4.83674 12.0038C5.13757 12.0038 5.44007 11.9621 5.7209 11.8896C4.9607 11.7361 4.27713 11.3241 3.78642 10.7235C3.29571 10.1229 3.02815 9.37097 3.02924 8.59543V8.55376C3.47674 8.80293 3.9959 8.95876 4.5459 8.9796C4.08514 8.67342 3.70734 8.25795 3.44619 7.77026C3.18504 7.28256 3.04866 6.73781 3.04924 6.1846C3.04924 5.56126 3.21507 4.9896 3.5059 4.49126C4.34935 5.52878 5.40132 6.37756 6.59368 6.98265C7.78604 7.58773 9.0922 7.93561 10.4276 8.00376C10.3759 7.75376 10.3442 7.4946 10.3442 7.2346C10.344 6.79373 10.4307 6.35715 10.5993 5.9498C10.7679 5.54245 11.0152 5.17233 11.3269 4.86059C11.6386 4.54885 12.0088 4.30161 12.4161 4.133C12.8235 3.96438 13.26 3.87771 13.7009 3.87793C14.6676 3.87793 15.5401 4.28293 16.1534 4.93793C16.9049 4.79261 17.6255 4.51828 18.2834 4.1271C18.0329 4.90278 17.5082 5.56052 16.8076 5.9771C17.4741 5.90108 18.1254 5.72581 18.7401 5.4571C18.281 6.12635 17.7122 6.71322 17.0576 7.19293Z"
              fill="currentColor" />
          </svg>
        </a>
        <a href="#" class="text-white" >
          <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_1833_185630)">
              <path
                d="M17.5869 6.33973C17.5774 5.62706 17.444 4.9215 17.1926 4.25456C16.9747 3.69202 16.6418 3.18112 16.2152 2.75453C15.7886 2.32793 15.2776 1.995 14.7151 1.77703C14.0568 1.5299 13.3613 1.39627 12.6582 1.38183C11.753 1.34137 11.466 1.33008 9.16819 1.33008C6.87039 1.33008 6.57586 1.33008 5.67725 1.38183C4.97451 1.39637 4.27932 1.53 3.62127 1.77703C3.05863 1.99485 2.54765 2.32772 2.12103 2.75434C1.69442 3.18096 1.36155 3.69193 1.14373 4.25456C0.896101 4.91242 0.76276 5.60776 0.749471 6.31056C0.70901 7.2167 0.696777 7.50368 0.696777 9.8015C0.696777 12.0993 0.696777 12.3928 0.749471 13.2924C0.763585 13.9963 0.89626 14.6907 1.14373 15.3503C1.36192 15.9128 1.69503 16.4236 2.1218 16.85C2.54855 17.2765 3.05957 17.6091 3.6222 17.8269C4.27846 18.084 4.97377 18.2272 5.67819 18.2504C6.58433 18.2908 6.87133 18.303 9.16913 18.303C11.4669 18.303 11.7615 18.303 12.6601 18.2504C13.3632 18.2365 14.0587 18.1032 14.717 17.8561C15.2794 17.6378 15.7902 17.3048 16.2167 16.8782C16.6433 16.4517 16.9763 15.941 17.1945 15.3785C17.442 14.7198 17.5746 14.0254 17.5888 13.3207C17.6293 12.4155 17.6414 12.1285 17.6414 9.82973C17.6396 7.53191 17.6396 7.24021 17.5869 6.33973ZM9.16255 14.1468C6.75935 14.1468 4.81251 12.2 4.81251 9.79679C4.81251 7.39359 6.75935 5.44676 9.16255 5.44676C10.3163 5.44676 11.4227 5.90506 12.2385 6.72085C13.0543 7.53664 13.5126 8.64309 13.5126 9.79679C13.5126 10.9505 13.0543 12.057 12.2385 12.8727C11.4227 13.6885 10.3163 14.1468 9.16255 14.1468ZM13.6857 6.3002C13.5525 6.30033 13.4206 6.27417 13.2974 6.22325C13.1743 6.17231 13.0624 6.09759 12.9682 6.00338C12.874 5.90917 12.7992 5.79729 12.7483 5.67417C12.6974 5.55105 12.6712 5.41909 12.6713 5.28585C12.6713 5.15271 12.6976 5.02087 12.7485 4.89786C12.7994 4.77485 12.8742 4.66308 12.9683 4.56893C13.0625 4.47479 13.1743 4.4001 13.2973 4.34915C13.4202 4.2982 13.5521 4.27197 13.6853 4.27197C13.8184 4.27197 13.9503 4.2982 14.0732 4.34915C14.1962 4.4001 14.3081 4.47479 14.4022 4.56893C14.4963 4.66308 14.571 4.77485 14.622 4.89786C14.6729 5.02087 14.6991 5.15271 14.6991 5.28585C14.6991 5.84666 14.2456 6.3002 13.6857 6.3002Z"
                fill="currentColor" />
              <path d="M9.16296 12.6226C10.7236 12.6226 11.9887 11.3575 11.9887 9.79688C11.9887 8.23629 10.7236 6.97119 9.16296 6.97119C7.60238 6.97119 6.33728 8.23629 6.33728 9.79688C6.33728 11.3575 7.60238 12.6226 9.16296 12.6226Z" fill="currentColor" />
            </g>
            <defs>
              <clipPath id="clip0_1833_185630">
                <rect width="16.9412" height="18" fill="currentColor" transform="translate(0.696777 0.528809)" />
              </clipPath>
            </defs>
          </svg>
        </a>
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->


    <script src="{{ asset('themes/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('themes/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    {{-- <script src="{{ asset('themes/sneat/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script> --}}

    
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    
      <script src="{{ asset('themes/sneat/assets/js/front-main.js') }}"></script>
    

    <!-- Page JS -->
    <script src="{{ asset('themes/sneat/assets/js/front-page-landing.js') }}"></script>
  </body>
</html>

  <!-- beautify ignore:end -->

