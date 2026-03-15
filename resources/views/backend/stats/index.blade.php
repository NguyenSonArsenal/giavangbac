@extends('backend.layout.main')

@push('css-3rd')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('style')
<style>
  .stats-cards { display:flex; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
  .stats-card { flex:1; min-width:140px; background:#1e2a3a; border-radius:10px; padding:16px; text-align:center; }
  .stats-card-label { font-size:12px; color:#6e778c; text-transform:uppercase; margin-bottom:4px; }
  .stats-card-val { font-size:28px; font-weight:700; }
  .stats-card-sub { font-size:11px; color:#909ab2; margin-top:2px; }
  .card-green .stats-card-val { color:#22c97a; }
  .card-blue .stats-card-val { color:#4f7af8; }
  .card-purple .stats-card-val { color:#a78bfa; }
  .card-gold .stats-card-val { color:#f5c518; }
  .card-red .stats-card-val { color:#f55252; }
  .chart-wrap { background:#1e2a3a; border-radius:10px; padding:20px; margin-bottom:24px; }
  .chart-title { font-weight:600; margin-bottom:14px; }
  .chart-box { height:280px; }
  .tbl-wrap { background:#1e2a3a; border-radius:10px; padding:20px; margin-bottom:24px; }
  .tbl-head { font-weight:600; margin-bottom:14px; }
  .tbl-wrap table { width:100%; font-size:13px; }
  .tbl-wrap th { color:#6e778c; text-transform:uppercase; font-size:10px; padding:8px; border-bottom:1px solid rgba(255,255,255,0.08); }
  .tbl-wrap td { padding:8px; border-bottom:1px solid rgba(255,255,255,0.04); }
  .mono { font-family:'JetBrains Mono',monospace; }
  .url-link { color:#4f7af8; text-decoration:none; }
  .url-link:hover { text-decoration:underline; }
  .bar { height:8px; background:rgba(255,255,255,0.06); border-radius:4px; overflow:hidden; min-width:60px; }
  .bar-fill { height:100%; background:linear-gradient(90deg,#4f7af8,#a78bfa); border-radius:4px; }
</style>
@endpush

@section('content')
<div class="content-page">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">📊 Thống Kê Lượt Truy Cập</h4>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- OVERVIEW --}}
        <div class="stats-cards">
            <div class="stats-card card-green">
                <div class="stats-card-label">Hôm nay</div>
                <div class="stats-card-val">{{ number_format($stats['today_views']) }}</div>
                <div class="stats-card-sub">{{ number_format($stats['today_unique']) }} unique IP</div>
            </div>
            <div class="stats-card card-blue">
                <div class="stats-card-label">Hôm qua</div>
                <div class="stats-card-val">{{ number_format($stats['yesterday_views']) }}</div>
                <div class="stats-card-sub">{{ number_format($stats['yesterday_unique']) }} unique IP</div>
            </div>
            <div class="stats-card card-purple">
                <div class="stats-card-label">7 ngày qua</div>
                <div class="stats-card-val">{{ number_format($stats['week_views']) }}</div>
                <div class="stats-card-sub">{{ number_format($stats['week_unique']) }} unique IP</div>
            </div>
            <div class="stats-card card-gold">
                <div class="stats-card-label">30 ngày qua</div>
                <div class="stats-card-val">{{ number_format($stats['month_views']) }}</div>
                <div class="stats-card-sub">{{ number_format($stats['month_unique']) }} unique IP</div>
            </div>
            <div class="stats-card">
                <div class="stats-card-label">Tổng cộng</div>
                <div class="stats-card-val" style="color:#e4e8f2">{{ number_format($stats['total_views']) }}</div>
                <div class="stats-card-sub">{{ number_format($stats['total_unique']) }} unique IP</div>
            </div>
            <div class="stats-card card-red">
                <div class="stats-card-label">Bot / Crawler</div>
                <div class="stats-card-val">{{ number_format($stats['bot_views']) }}</div>
                <div class="stats-card-sub">Tự động loại khỏi thống kê</div>
            </div>
        </div>

        {{-- CHART --}}
        <div class="chart-wrap">
            <div class="chart-title">📈 Lượt truy cập 30 ngày qua</div>
            <div class="chart-box"><canvas id="daily-chart"></canvas></div>
        </div>

        {{-- TOP PAGES --}}
        <div class="tbl-wrap">
            <div class="tbl-head">🏆 Top trang được xem nhiều nhất (30 ngày)</div>
            <table>
                <thead><tr><th>#</th><th>URL</th><th>Lượt xem</th><th>Unique IP</th><th>Tỷ lệ</th></tr></thead>
                <tbody>
                @php $maxViews = $topPages->max('views') ?: 1; @endphp
                @foreach($topPages as $i => $page)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><a href="{{ $page->url }}" class="url-link" target="_blank">{{ $page->url }}</a></td>
                        <td class="mono">{{ number_format($page->views) }}</td>
                        <td class="mono">{{ number_format($page->unique_ips) }}</td>
                        <td>
                            <div class="bar"><div class="bar-fill" style="width:{{ round($page->views / $maxViews * 100) }}%"></div></div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- RECENT --}}
        <div class="tbl-wrap">
            <div class="tbl-head">🕐 50 lượt truy cập gần nhất</div>
            <table>
                <thead><tr><th>Thời gian</th><th>URL</th><th>IP</th><th>Referer</th></tr></thead>
                <tbody>
                @foreach($recent as $r)
                    <tr>
                        <td class="mono">{{ $r->created_at->format('H:i:s d/m') }}</td>
                        <td><a href="{{ $r->url }}" class="url-link" target="_blank">{{ Str::limit($r->url, 35) }}</a></td>
                        <td class="mono">{{ $r->ip }}</td>
                        <td style="color:#6e778c;font-size:11px">{{ Str::limit($r->referer, 30) ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@push('script')
<script>
(function(){
  var labels = @json($dailyViews->pluck('date'));
  var views  = @json($dailyViews->pluck('views'));
  var uniq   = @json($dailyViews->pluck('unique_ips'));

  new Chart(document.getElementById('daily-chart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Lượt xem',
          data: views,
          borderColor: '#4f7af8',
          backgroundColor: 'rgba(79,122,248,0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 6,
        },
        {
          label: 'Unique IP',
          data: uniq,
          borderColor: '#a78bfa',
          backgroundColor: 'rgba(167,139,250,0.06)',
          borderWidth: 2,
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 6,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top', labels: { color: '#909ab2', font: { size: 12 } } },
      },
      scales: {
        x: { ticks: { color: '#6e778c', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
        y: { ticks: { color: '#6e778c', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.06)' }, beginAtZero: true }
      }
    }
  });
})();
</script>
@endpush
