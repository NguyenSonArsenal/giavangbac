@extends('frontend.partials.layout', ['activePage' => 'contact'])

@section('title', 'Liên hệ – GiáVàng.vn')

@section('meta')
  <meta name="description" content="Liên hệ với GiáVàng.vn – Gửi tin nhắn, câu hỏi hoặc góp ý cho chúng tôi."/>
@endsection

@push('styles')
<style>
  .contact-page-section { margin-top: -16px; }
</style>
@endpush

@section('content')
<div class="contact-page-section">
  @include('frontend.partials.contact-section', ['variant' => 'page'])
</div>
@stop
