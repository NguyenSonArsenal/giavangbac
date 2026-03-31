<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  @if(config('services.ga.measurement_id'))
  <!-- Google Analytics 4 -->
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga.measurement_id') }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ config('services.ga.measurement_id') }}');
  </script>
  @endif
  <link rel="icon" type="image/x-icon" href="{{ asset('frontend/image/favicon.png') }}">
  <title>@yield('title', 'Giá Vàng & Bạc – GiáVàng.vn')</title>
  <meta name="robots" content="index, follow"/>
  <meta property="og:site_name" content="GiáVàng.vn"/>
  <meta property="og:locale" content="vi_VN"/>
  @yield('meta')
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/frontend/css/app.css"/>
  @stack('styles')
  @stack('head-scripts')
</head>
<body>
@hasSection('bg-glow')
  @yield('bg-glow')
@else
  <div class="bg-glow" style="position:fixed;inset:0;pointer-events:none;z-index:0;
    background:radial-gradient(ellipse 70% 45% at 15% 0%,rgba(79,122,248,0.08) 0%,transparent 60%),
               radial-gradient(ellipse 60% 40% at 85% 90%,rgba(245,197,24,0.05) 0%,transparent 60%)"></div>
@endif

@include('frontend.partials.header', ['activePage' => $activePage ?? ''])

<main style="position:relative;z-index:1;max-width:{{ $maxWidth ?? '1200px' }};margin:0 auto;padding:40px 24px 80px">
  @yield('content')
</main>

@include('frontend.partials.footer')

@include('frontend.partials.floating-contact')

@stack('scripts')
</body>
</html>
