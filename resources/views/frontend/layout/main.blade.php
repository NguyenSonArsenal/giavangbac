<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- One-Time Nonce Token – dùng để xác thực mọi API request --}}
  <title>Pharma</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('frontend/image/favicon.jpg') }}">

  {{--  @todo download font Roboto to local--}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="{{ asset('frontend/vendor/swiper/swiper-bundle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/css/index.css') }}">

  @stack('style')
</head>
<body>
<main class="main">
  @include('frontend.layout.header')

  @yield('content')

  @if (!Str::contains(request()->route()->getName(), ['auth.', 'cart', 'tin_tuc', 'san_pham', 'checkout', 'account']))
    @include('frontend.layout.section_doctor')
  @endif

  @include('frontend.layout.footer')
</main>
</body>

<script src="{{ asset('frontend/vendor/jquery/jquery-3.7.1.min.js')  }}"></script>
<script src="{{ asset('frontend/vendor/swiper/swiper-bundle.min.js')  }}"></script>
<script src="{{ asset('frontend/js/common.js')  }}"></script>

<script type="text/javascript">
  // ── One-Time Nonce Token Manager ─────────────────────────────────────────
  // Token được nhúng vào trang khi load, chỉ dùng 1 lần duy nhất.
  // Sau mỗi request thành công, server trả về token mới trong header X-Api-Nonce-Next.
  // JS tự động cập nhật token → request tiếp theo luôn có token hợp lệ.
  window._apiNonce = $('meta[name="api-nonce"]').attr('content');

  // ── jQuery AJAX interceptor – tự động đính kèm nonce vào mọi API call ────
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function(xhr) {
      // Đính kèm nonce token hiện tại
      if (window._apiNonce) {
        xhr.setRequestHeader('X-Api-Nonce', window._apiNonce);
      }
    },
    complete: function(xhr) {
      // Đọc token mới từ response header, lưu lại cho request kế tiếp
      var nextNonce = xhr.getResponseHeader('X-Api-Nonce-Next');
      if (nextNonce) {
        window._apiNonce = nextNonce;
      }
    }
  });
</script>

@stack('script')
</html>
