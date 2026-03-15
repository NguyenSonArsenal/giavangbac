@extends('frontend.partials.layout', ['activePage' => 'article', 'maxWidth' => '1200px'])

@section('title', 'Bài Viết | GiáVàng.vn')

@section('meta')
<meta name="description" content="Các bài viết, kiến thức về vàng bạc, đầu tư, và thị trường kim loại quý tại GiáVàng.vn"/>
@endsection

@push('styles')
<style>
/* ── Page Header ── */
.posts-header { margin-bottom: 36px }
.posts-header h1 {
  font-size: 28px; font-weight: 800; margin: 0 0 6px;
  background: linear-gradient(90deg, #ffd76e, #f5c518);
  -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
}
.posts-header p { color: var(--muted2); font-size: 15px; margin: 0 }

/* ── Grid ── */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}
@media (max-width: 1100px) { .posts-grid { grid-template-columns: repeat(3, 1fr) } }
@media (max-width: 768px)  { .posts-grid { grid-template-columns: repeat(2, 1fr); gap: 14px } }
@media (max-width: 480px)  { .posts-grid { grid-template-columns: 1fr } }

/* ── Card ── */
.post-card {
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  transition: border-color .25s, transform .25s, box-shadow .25s;
}
.post-card:hover {
  border-color: var(--blue);
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(79,122,248,0.12);
}

/* Thumbnail */
.post-card__thumb {
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  position: relative;
  overflow: hidden;
}
.post-card__thumb::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 40px;
  background: linear-gradient(to top, var(--bg3), transparent);
}
@media (max-width: 480px) { .post-card__thumb { height: 130px } }

/* Card body */
.post-card__body {
  padding: 16px 18px 18px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* Meta tags */
.post-card__tags {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}
.post-card__tag {
  font-size: 10.5px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 4px;
  background: rgba(79,122,248,0.12);
  color: var(--blue);
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.post-card__views {
  font-size: 11px;
  color: var(--muted);
  margin-left: auto;
}

/* Title */
.post-card__title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.45;
  margin: 0 0 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.post-card:hover .post-card__title { color: var(--blue) }

/* Description */
.post-card__des {
  font-size: 13px;
  color: var(--muted2);
  line-height: 1.6;
  margin: 0 0 14px;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Read more */
.post-card__more {
  font-size: 13px;
  font-weight: 600;
  color: var(--blue);
  display: flex;
  align-items: center;
  gap: 4px;
}
.post-card:hover .post-card__more { gap: 8px }
.post-card__more span { transition: transform .2s }
.post-card:hover .post-card__more span { transform: translateX(3px) }

/* Date */
.post-card__date {
  font-size: 11px;
  color: var(--muted);
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--border);
}

/* ── Pagination ── */
.posts-pagination {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}
.posts-pagination ul.pagination {
  display: flex;
  gap: 6px;
  list-style: none;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
  justify-content: center;
}
.posts-pagination .page-item { list-style: none }
.posts-pagination .page-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 38px;
  height: 38px;
  background: var(--bg3);
  border: 1px solid var(--border);
  color: var(--text2);
  border-radius: 8px;
  padding: 0 12px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all .2s;
}
.posts-pagination .page-item.active .page-link {
  background: var(--blue);
  border-color: var(--blue);
  color: #fff;
}
.posts-pagination .page-item.disabled .page-link {
  opacity: 0.4;
  pointer-events: none;
}
.posts-pagination .page-link:hover {
  background: rgba(79,122,248,0.15);
  border-color: var(--blue);
  color: var(--blue);
}

/* Empty state */
.posts-empty {
  text-align: center;
  color: var(--muted);
  padding: 80px 0;
  font-size: 16px;
  grid-column: 1 / -1;
}
</style>
@endpush

@section('content')
  <div class="posts-header">
    <h1>Tin Tức & Kiến Thức</h1>
    <p>Cập nhật thị trường vàng bạc, phân tích đầu tư, và kiến thức tài chính</p>
  </div>

  @php
    $gradients = [
      'linear-gradient(135deg, #f5c518 0%, #c8820a 100%)',
      'linear-gradient(135deg, #4f7af8 0%, #2563eb 100%)',
      'linear-gradient(135deg, #22c97a 0%, #059669 100%)',
      'linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%)',
      'linear-gradient(135deg, #f97316 0%, #dc2626 100%)',
      'linear-gradient(135deg, #06b6d4 0%, #0284c7 100%)',
      'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
      'linear-gradient(135deg, #b0bec5 0%, #546e7a 100%)',
    ];
    $icons = ['📊','💰','📈','🥇','🏦','⚡','📰','🔔','💎','🛡️','🌍','📉'];
    $tags = ['Thị trường','Đầu tư','Phân tích','Kiến thức','Tin tức','Hướng dẫn'];
  @endphp

  @if($posts->count() > 0)
    <div class="posts-grid">
      @foreach($posts as $i => $post)
        <a href="{{ route('fe.post.show', $post->slug) }}" class="post-card">
          <div class="post-card__thumb" style="background:{{ $gradients[$i % count($gradients)] }}">
            {{ $icons[$i % count($icons)] }}
          </div>
          <div class="post-card__body">
            <div class="post-card__tags">
              <span class="post-card__tag">{{ $tags[$i % count($tags)] }}</span>
              <span class="post-card__views">👁 {{ rand(120, 980) }}</span>
            </div>
            <h2 class="post-card__title">{{ $post->title }}</h2>
            @if($post->des)
              <p class="post-card__des">{{ $post->des }}</p>
            @endif
            <div class="post-card__more">
              Xem thêm <span>→</span>
            </div>
            <div class="post-card__date">
              📅 {{ $post->created_at->format('d/m/Y') }} · {{ getReadingMinutes($post->content) }} phút đọc
            </div>
          </div>
        </a>
      @endforeach
    </div>

    <div class="posts-pagination">
      {{ $posts->links('pagination::bootstrap-4') }}
    </div>
  @else
    <div class="posts-grid">
      <div class="posts-empty">
        Chưa có bài viết nào. Hãy quay lại sau nhé! 🙏
      </div>
    </div>
  @endif
@endsection
