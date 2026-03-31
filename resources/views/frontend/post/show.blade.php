@extends('frontend.partials.layout', ['activePage' => 'article', 'maxWidth' => '800px'])

@section('title', $post->title . ' | GiáVàng.vn')

@section('meta')
<meta name="description" content="{{ $post->des ?? Str::limit(strip_tags($post->content), 160) }}"/>
<link rel="canonical" href="{{ url('/bai-viet/' . $post->slug) }}"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="{{ $post->title }} | GiáVàng.vn"/>
<meta property="og:description" content="{{ $post->des ?? Str::limit(strip_tags($post->content), 160) }}"/>
<meta property="og:url" content="{{ url('/bai-viet/' . $post->slug) }}"/>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $post->title }}",
  "description": "{{ $post->des ?? Str::limit(strip_tags($post->content), 160) }}",
  "url": "{{ url('/bai-viet/' . $post->slug) }}",
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "publisher": {
    "@type": "Organization",
    "name": "GiáVàng.vn",
    "url": "{{ url('/') }}"
  }
}
</script>
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

/* Category badge */
.post-header__cat {
  display: inline-flex; align-items: center; gap: 5px;
  margin-bottom: 14px;
}
.post-header__cat a {
  display: inline-block;
  padding: 5px 14px; border-radius: 20px;
  font-size: 12px; font-weight: 700;
  background: rgba(79,122,248,0.12);
  border: 1px solid rgba(79,122,248,0.25);
  color: #7da0fa; text-decoration: none;
  transition: all .2s;
}
.post-header__cat a:hover {
  background: rgba(79,122,248,0.22);
  border-color: rgba(79,122,248,0.45);
  color: #93b4ff; text-decoration: none;
}

/* Prev / Next */
.post-nav {
  display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
  margin-top: 40px; padding-top: 28px;
  border-top: 1px solid var(--border);
}
@media (max-width: 600px) { .post-nav { grid-template-columns: 1fr; } }
.post-nav-item a {
  display: block; padding: 16px 20px;
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 10px;
  text-decoration: none; color: inherit;
  transition: border-color .2s, box-shadow .2s, transform .2s;
}
.post-nav-item a:hover {
  border-color: rgba(79,122,248,0.4);
  box-shadow: 0 4px 16px rgba(79,122,248,0.1);
  transform: translateY(-2px);
  text-decoration: none;
}
.post-nav-label {
  display: block; font-size: 11.5px; font-weight: 700;
  color: var(--blue); text-transform: uppercase;
  letter-spacing: 0.04em; margin-bottom: 6px;
}
.post-nav-title {
  display: block; font-size: 14px; font-weight: 600;
  color: var(--text2); line-height: 1.4;
  overflow: hidden; text-overflow: ellipsis;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.post-nav-next { text-align: right; }
</style>
@endpush

@section('content')
  <div class="post-header">
    @if($post->category)
      <div class="post-header__cat">
        <a href="{{ route('fe.category.show', $post->category->slug) }}">📂 {{ $post->category->name }}</a>
      </div>
    @endif
    <h1 class="post-header__title">{{ $post->title }}</h1>
    <div class="post-header__meta">
      📅 {{ $post->created_at->format('d/m/Y H:i') }}
      · ⏱ {{ getReadingMinutes($post->content) }} phút đọc
    </div>
  </div>

  <div class="post-content">
    {!! $post->content !!}
  </div>

  @if($post->category)
    <div class="post-header__cat" style="margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--border);">
      <a href="{{ route('fe.category.show', $post->category->slug) }}">📂 {{ $post->category->name }}</a>
    </div>
  @endif

  {{-- Prev / Next navigation --}}
  <div class="post-nav">
    <div class="post-nav-item post-nav-prev">
      @if($prevPost)
        <a href="{{ route('fe.post.show', $prevPost->slug) }}">
          <span class="post-nav-label">← Bài trước</span>
          <span class="post-nav-title">{{ $prevPost->title }}</span>
        </a>
      @endif
    </div>
    <div class="post-nav-item post-nav-next">
      @if($nextPost)
        <a href="{{ route('fe.post.show', $nextPost->slug) }}">
          <span class="post-nav-label">Bài tiếp theo →</span>
          <span class="post-nav-title">{{ $nextPost->title }}</span>
        </a>
      @endif
    </div>
  </div>

  {{-- Comment section --}}
  @include('frontend.partials.comment-section')

  <a href="{{ route('fe.post.index') }}" class="post-back">← Quay lại danh sách bài viết</a>
@endsection
