{{-- Biến truyền vào: $paginator --}}
<style>
  .pagination {
    text-align: center;
    margin-top: 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: center;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    transition: all 0.3s cubic-bezier(.25, .8, .25, 1);
    flex-wrap: wrap;
    background: #fff;
    gap: 12px;
  }

  .page-item:hover {
    background: #0f6a3a;
    color: white !important;
  }
  .pagination .page-item.active, .pagination .page-item:hover {
    color: #fff;
    background: #0f6a3a;
  }
  .pagination .page-item {
    font-weight: bold;
    border-radius: 5px;
    border: 1px solid #0f6a3a;
    display: inline-flex;
    width: 36px;
    height: 28px;
    align-items: center;
    justify-content: center;
    color: #0f6a3a;
  }

  .page-items { display: inline-flex; gap: 4px; align-items: center; }

  /* Toggle cụm desktop/mobile */
  .page-items--mobile { display: none; }

  @media (max-width: 600px) {
    /* Ép 1 dòng */
    .pagination{
      flex-wrap: nowrap;
      gap: 6px;
      padding: 10px;
    }

    .page-items { gap: 6px; }

    /* Thu nhỏ một chút cho vừa */
    .pagination .page-item{
      width: 32px;
      height: 28px;
      font-size: 13px;
    }

    /* Chỉ hiện cụm mobile */
    .page-items--desktop { display: none; }
    .page-items--mobile { display: inline-flex; }
  }
</style>

@if ($paginator->hasPages())
  <div class="pagination news_paging">
    {{-- Trang trước --}}
    @if ($paginator->onFirstPage())
      <span class="paging_prev disabled page-item"><i class="fa fa-light fa-angle-left"></i></span>
    @else
      <a class="paging_prev page-item" href="{{ $paginator->previousPageUrl() }}"><i class="fa fa-light fa-angle-left"></i></a>
    @endif

    {{-- ====== DESKTOP: 5 phần tử quanh current (-2..+2) ====== --}}
    <div class="page-items page-items--desktop">
      {{-- Trang đầu --}}
      @if ($paginator->currentPage() > 3)
        <a class="page-item" href="{{ $paginator->url(1) }}">1</a>
        <span class="paging_dots page-item">…</span>
      @endif

      {{-- Các trang gần hiện tại --}}
      @for ($i = max(1, $paginator->currentPage() - 2); $i <= min($paginator->lastPage(), $paginator->currentPage() + 2); $i++)
        @if ($i == $paginator->currentPage())
          <a class="page-item active" href="#">{{ $i }}</a>
        @else
          <a class="page-item" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        @endif
      @endfor

      {{-- Trang cuối --}}
      @if ($paginator->currentPage() < $paginator->lastPage() - 2)
        <span class="paging_dots page-item">…</span>
        <a class="page-item" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
      @endif
    </div>

    {{-- ====== MOBILE: 3 phần tử quanh current (-1..+1) ====== --}}
    <div class="page-items page-items--mobile">
      {{-- Trang đầu --}}
      @if ($paginator->currentPage() > 2)
        <a class="page-item" href="{{ $paginator->url(1) }}">1</a>
        <span class="paging_dots page-item">…</span>
      @endif

      {{-- 3 trang gần hiện tại --}}
      @for ($i = max(1, $paginator->currentPage() - 1); $i <= min($paginator->lastPage(), $paginator->currentPage() + 1); $i++)
        @if ($i == $paginator->currentPage())
          <a class="page-item active" href="#">{{ $i }}</a>
        @else
          <a class="page-item" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        @endif
      @endfor

      {{-- Trang cuối --}}
      @if ($paginator->currentPage() < $paginator->lastPage() - 1)
        <span class="paging_dots page-item">…</span>
        <a class="page-item" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
      @endif
    </div>

    {{-- Trang sau --}}
    @if ($paginator->hasMorePages())
      <a class="paging_next page-item" href="{{ $paginator->nextPageUrl() }}"><i class="fa fa-light fa-angle-right"></i></a>
    @else
      <span class="paging_next disabled page-item"><i class="fa fa-light fa-angle-right"></i></span>
    @endif
  </div>
@endif
