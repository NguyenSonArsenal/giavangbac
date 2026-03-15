@extends('frontend.partials.layout', ['activePage' => 'article', 'maxWidth' => '800px'])

@section('title', $post->title . ' | GiáVàng.vn')

@section('meta')
<meta name="description" content="{{ $post->des ?? Str::limit(strip_tags($post->content), 160) }}"/>
@endsection

@push('styles')
<link rel="stylesheet" href="/frontend/css/tinymce_editor.css"/>
<style>
.post-header {
  margin-bottom: 32px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}
.post-header__title {
  font-size: 32px;
  font-weight: 800;
  line-height: 1.25;
  margin: 0 0 12px;
  color: var(--text);
}
.post-header__meta {
  font-size: 13px;
  color: var(--muted);
}
.post-content {
  font-size: 16px;
  line-height: 1.85;
  color: var(--text2);
}
.post-content h2 { color: var(--text); }
.post-content h3 { color: var(--text); }
.post-content a { color: var(--blue); }
.post-content img {
  border-radius: 10px;
  margin: 20px auto;
}
.post-content blockquote {
  border-left: 4px solid var(--blue);
  background: rgba(79,122,248,0.06);
  padding: 14px 18px;
  border-radius: 0 10px 10px 0;
  color: var(--text2);
  margin: 18px 0;
}
.post-back {
  display: inline-block;
  margin-top: 40px;
  color: var(--blue);
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
}
.post-back:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
  <div class="post-header">
    <h1 class="post-header__title">{{ $post->title }}</h1>
    <div class="post-header__meta">
      📅 {{ $post->created_at->format('d/m/Y H:i') }}
      · ⏱ {{ getReadingMinutes($post->content) }} phút đọc
    </div>
  </div>

  <div class="post-content">
    {!! $post->content !!}
  </div>

  <a href="{{ route('fe.post.index') }}" class="post-back">← Quay lại danh sách bài viết</a>
@endsection
