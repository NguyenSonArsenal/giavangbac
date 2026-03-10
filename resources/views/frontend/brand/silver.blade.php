<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $brand['title'] }}</title>
  <meta name="description" content="{{ $brand['description'] }}" />
  <link rel="canonical" href="{{ url('/' . $brand['slug']) }}" />

  {{-- Open Graph --}}
  <meta property="og:type"        content="website" />
  <meta property="og:title"       content="{{ $brand['title'] }}" />
  <meta property="og:description" content="{{ $brand['description'] }}" />
  <meta property="og:url"         content="{{ url('/' . $brand['slug']) }}" />
  <meta property="og:site_name"   content="GiáVàng.vn" />

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  {{-- JSON-LD: FAQPage --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      @foreach($brand['faqs'] as $i => $faq)
      {
        "@type": "Question",
        "name": "{{ $faq['q'] }}",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "{{ $faq['a'] }}"
        }
      }{{ !$loop->last ? ',' : '' }}
      @endforeach
    ]
  }
  </script>

  {{-- JSON-LD: WebPage --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "{{ $brand['title'] }}",
    "description": "{{ $brand['description'] }}",
    "url": "{{ url('/' . $brand['slug']) }}",
    "publisher": {
      "@type": "Organization",
      "name": "GiáVàng.vn",
      "url": "{{ url('/') }}"
    }
  }
  </script>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:      #07090f;
      --bg2:     #0d1018;
      --bg3:     #131724;
      --bg4:     #1a2030;
      --border:  rgba(255,255,255,0.07);
      --border2: rgba(255,255,255,0.12);
      --gold:    #f5c518;
      --silver:  #b0bec5;
      --silver2: #dde6ed;
      --text:    #e4e8f2;
      --text2:   #c4cad8;
      --muted:   #6e778c;
      --muted2:  #909ab2;
      --green:   #22c97a;
      --red:     #f55252;
      --blue:    #4f7af8;
      --brand:   {{ $brand['color'] }};
      --radius:  14px;
      --radius-sm: 8px;
      --shadow:  0 8px 40px rgba(0,0,0,0.55);
      --shadow-sm: 0 4px 16px rgba(0,0,0,0.35);
    }

    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    .bg-glow {
      position: fixed; inset: 0; pointer-events: none; z-index: 0;
      background:
        radial-gradient(ellipse 60% 40% at 20% 0%, rgba(79,122,248,0.07) 0%, transparent 60%),
        radial-gradient(ellipse 50% 35% at 80% 90%, rgba(var(--brand-rgb, 176,190,197),0.05) 0%, transparent 60%);
    }


    /* ── MAIN ── */
    main {
      position:relative; z-index:1;
      max-width:960px; margin:0 auto;
      padding:40px 24px 80px;
    }
    @media(max-width:640px) {
      main { padding:12px 0 60px; }
      .breadcrumb{padding:0 12px}
      .page-head{padding:0 12px}
      .unit-tabs{padding:0 12px}
      .price-table-wrap{border-radius:0;border-left:none;border-right:none}
      .chart-section{border-radius:0;border-left:none;border-right:none}
      .about-section{border-radius:0;border-left:none;border-right:none}
      .faq-item{border-radius:0;border-left:none;border-right:none}
      .faq-section h2{padding:0 12px}
      .related-section{padding:0 12px}
    }

    /* ── BREADCRUMB ── */
    .breadcrumb {
      font-size:12px; color:var(--muted); margin-bottom:20px;
      display:flex; align-items:center; gap:6px; flex-wrap:wrap;
    }
    .breadcrumb a { color:var(--muted); text-decoration:none; }
    .breadcrumb a:hover { color:var(--text2); }
    .breadcrumb .sep { color:var(--muted); }

    /* ── PAGE HEADER ── */
    .page-head { margin-bottom:28px; }
    .page-head-row { display:flex; align-items:center; gap:14px; margin-bottom:6px; }
    .brand-icon {
      width:48px; height:48px; border-radius:12px; flex-shrink:0;
      background: {{ $brand['gradient'] }};
      display:flex; align-items:center; justify-content:center;
      font-size:{{ strlen($brand['icon']) > 2 ? '13px' : '22px' }}; font-weight:900;
      color:{{ strlen($brand['icon']) > 2 ? '#fff' : 'inherit' }};
      box-shadow: 0 4px 16px rgba(0,0,0,0.4);
    }
    .page-head h1 { font-size:24px; font-weight:800; letter-spacing:-.4px; line-height:1.3; }
    .page-head .sub { font-size:13px; color:var(--muted); margin-top:5px; }
    @media(max-width:640px) { .page-head h1 { font-size:19px; } }

    /* ── SSR PRICE TABLE ── */
    .price-table-wrap {
      background: var(--bg2);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: var(--radius);
      overflow: hidden;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
      border-top: 2px solid {{ $brand['color'] }};
    }
    .price-table-head {
      padding:14px 20px 12px;
      background: linear-gradient(90deg, rgba(255,255,255,0.03), transparent);
      border-bottom: 1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
    }
    .price-table-head h2 { font-size:14px; font-weight:700; color:var(--text2); }
    .updated-label { font-size:11px; color:var(--muted); }

    table.price-tbl {
      width:100%; border-collapse:collapse;
    }
    table.price-tbl thead th {
      padding:10px 20px;
      font-size:11px; font-weight:700; text-transform:uppercase;
      letter-spacing:.07em; color:var(--muted);
      background:var(--bg3); text-align:left;
      border-bottom: 1px solid var(--border);
    }
    table.price-tbl tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background .15s;
    }
    table.price-tbl tbody tr:last-child { border-bottom:none; }
    table.price-tbl tbody tr:hover { background:rgba(255,255,255,0.02); }
    table.price-tbl tbody td {
      padding:14px 20px;
      font-size:14px; color:var(--text2);
      vertical-align:middle;
    }
    .td-unit {
      font-weight:600; font-size:13px;
      color: {{ $brand['color'] }};
    }
    .td-price {
      font-family:'JetBrains Mono',monospace;
      font-size:18px; font-weight:700;
    }
    .td-buy  { color: var(--red); }
    .td-sell { color: var(--green); }
    .td-spread { font-size:12px; color:var(--muted2); font-family:'JetBrains Mono',monospace; }
    .td-time { font-size:11px; color:var(--muted); }

    /* ── UNIT TABS ── */
    .unit-tabs { display:flex; gap:6px; margin-bottom:16px; }
    .unit-btn {
      padding:6px 16px; border-radius:var(--radius-sm);
      font-size:12.5px; font-weight:600;
      border:1px solid var(--border); background:var(--bg3);
      color:var(--muted2); cursor:pointer; transition:all .18s;
      font-family:'Inter',sans-serif;
    }
    .unit-btn:hover { border-color:var(--border2); color:var(--text); }
    .unit-btn.active {
      background: {{ $brand['gradient'] }};
      color:#fff; border-color:transparent;
      box-shadow:0 2px 10px rgba(0,0,0,0.3);
    }

    /* ── CHART SECTION ── */
    .chart-section {
      background:var(--bg2);
      border:1px solid var(--border);
      border-radius:var(--radius);
      overflow:hidden; margin-bottom:24px;
    }
    .chart-bar {
      display:flex; align-items:center; gap:8px;
      padding:12px 16px; border-bottom:1px solid var(--border);
      flex-wrap:wrap;
    }
    .chart-bar-title { font-size:13px; font-weight:600; color:var(--text2); margin-right:4px; }
    .prd-btn {
      padding:4px 12px; border-radius:5px;
      font-size:12px; font-weight:600;
      border:1px solid var(--border); background:var(--bg3);
      color:var(--muted2); cursor:pointer; transition:all .18s;
      font-family:'Inter',sans-serif;
    }
    .prd-btn:hover { border-color:var(--border2); color:var(--text); }
    .prd-btn.active {
      background:rgba(79,122,248,0.18);
      color:var(--blue); border-color:rgba(79,122,248,0.4);
    }
    .chart-canvas-wrap {
      padding:12px 16px 16px;
      position:relative; height:280px;
    }
    .chart-canvas-wrap canvas { width:100%!important; height:100%!important; }
    .chart-loading {
      display:flex; align-items:center; justify-content:center;
      height:100%; color:var(--muted); font-size:13px; gap:8px;
    }
    .spinner {
      width:18px; height:18px; border:2px solid var(--border2);
      border-top-color:var(--silver); border-radius:50%;
      animation:spin .8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* ── ABOUT SECTION ── */
    .about-section {
      background:var(--bg2);
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:24px; margin-bottom:24px;
    }
    .about-section h2 {
      font-size:16px; font-weight:700; margin-bottom:12px;
      color:var(--text2);
    }
    .about-section p {
      font-size:14px; line-height:1.75; color:var(--muted2);
    }

    /* ── FAQ SECTION ── */
    .faq-section { margin-bottom:32px; }
    .faq-section h2 {
      font-size:18px; font-weight:800; margin-bottom:16px;
    }
    .faq-item {
      background:var(--bg2);
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      margin-bottom:8px; overflow:hidden;
    }
    .faq-q {
      padding:14px 16px; font-size:14px; font-weight:600;
      cursor:pointer; display:flex; align-items:center; justify-content:space-between;
      gap:12px; color:var(--text);
      transition:background .15s;
    }
    .faq-q:hover { background:rgba(255,255,255,0.03); }
    .faq-caret { font-size:12px; color:var(--muted); transition:transform .2s; flex-shrink:0; }
    .faq-item.open .faq-caret { transform:rotate(180deg); }
    .faq-a {
      padding:0 16px; font-size:13.5px; line-height:1.7; color:var(--muted2);
      max-height:0; overflow:hidden; transition:max-height .25s ease, padding .2s;
    }
    .faq-item.open .faq-a { max-height:200px; padding:0 16px 14px; }

    /* ── RELATED BRANDS ── */
    .related-section { margin-bottom:32px; }
    .related-section h2 { font-size:16px; font-weight:700; margin-bottom:12px; color:var(--text2); }
    .related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:10px; }
    .related-card {
      background:var(--bg2); border:1px solid var(--border);
      border-radius:var(--radius-sm); padding:12px 14px;
      text-decoration:none; display:flex; align-items:center; gap:10px;
      transition:border-color .18s, background .18s;
    }
    .related-card:hover { border-color:var(--border2); background:var(--bg3); }
    .related-icon {
      width:30px; height:30px; border-radius:7px; flex-shrink:0;
      display:flex; align-items:center; justify-content:center; font-size:14px;
    }
    .related-name { font-size:12.5px; font-weight:600; color:var(--text2); }
    .related-sub  { font-size:10.5px; color:var(--muted); margin-top:1px; }

    /* ── FOOTER ── */
    footer {
      border-top:1px solid var(--border);
      padding:20px 24px; text-align:center;
      font-size:12px; color:var(--muted);
    }
    footer a { color:var(--muted2); text-decoration:none; }
    footer a:hover { color:var(--text); }
  </style>
</head>
<body>
<div class="bg-glow"></div>

{{-- HEADER --}}
@include('frontend.partials.header', ['activePage' => $brand['key']])

<main>

  {{-- BREADCRUMB --}}
  <div class="breadcrumb">
    <a href="/">Trang chủ</a>
    <span class="sep">›</span>
    <span>{{ $brand['name'] }}</span>
  </div>

  {{-- PAGE HEADER --}}
  <div class="page-head">
    <div class="page-head-row">
      <div class="brand-icon">{{ $brand['icon'] }}</div>
      <h1>Giá Bạc {{ $brand['name_short'] }} 999 Hôm Nay – {{ now()->format('d/m/Y') }}</h1>
    </div>
    <p class="sub">Cập nhật real-time · Nguồn: {{ $brand['name_short'] }} chính thức · GiáVàng.vn</p>
  </div>

  {{-- SSR PRICE TABLE — Google đọc được --}}
  <div class="price-table-wrap">
    <div class="price-table-head">
      <h2>📊 Bảng Giá Bạc {{ $brand['name_short'] }} 999 – {{ now()->format('d/m/Y H:i') }}</h2>
      <span class="updated-label" id="tbl-updated">
        @if(!empty($prices))
          @php $firstPrice = collect($prices)->first(); @endphp
          Cập nhật: {{ $firstPrice->recorded_at ? $firstPrice->recorded_at->format('H:i d/m/Y') : 'N/A' }}
        @else
          Đang cập nhật...
        @endif
      </span>
    </div>

    <table class="price-tbl">
      <thead>
        <tr>
          <th>Đơn vị</th>
          <th>Mua vào (VNĐ)</th>
          <th>Bán ra (VNĐ)</th>
          <th>Chênh lệch</th>
          <th>Cập nhật</th>
        </tr>
      </thead>
      <tbody id="ssr-tbody">
        @forelse($prices as $unit => $row)
        <tr>
          <td class="td-unit">{{ $brand['unit_labels'][$unit] ?? $unit }}</td>
          <td class="td-price td-buy"  id="ssr-buy-{{ $unit }}">{{ number_format($row->buy_price) }}</td>
          <td class="td-price td-sell" id="ssr-sell-{{ $unit }}">{{ number_format($row->sell_price) }}</td>
          <td class="td-spread"        id="ssr-spread-{{ $unit }}">{{ number_format($row->sell_price - $row->buy_price) }}</td>
          <td class="td-time">{{ $row->recorded_at ? $row->recorded_at->format('H:i d/m') : '–' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted)">Đang tải dữ liệu...</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- CHART --}}
  <div class="chart-section">
    <div class="chart-bar">
      <span class="chart-bar-title">Lịch sử giá bán ra</span>
      @foreach($brand['chart_unit_options'] as $opt)
      <button class="unit-btn {{ $opt['active'] ? 'active' : '' }}"
              data-unit="{{ $opt['unit'] }}" data-mult="{{ $opt['mult'] }}">
        {{ $opt['label'] }}
      </button>
      @endforeach
      <span style="margin-left:auto"></span>
      <button class="prd-btn" data-days="1">1D</button>
      <button class="prd-btn active" data-days="7">7D</button>
      <button class="prd-btn" data-days="30">1M</button>
      <button class="prd-btn" data-days="90">3M</button>
      <button class="prd-btn" data-days="365">1Y</button>
    </div>
    <div class="chart-canvas-wrap">
      <div class="chart-loading" id="chart-loading">
        <div class="spinner"></div> Đang tải biểu đồ...
      </div>
      <canvas id="brandChart" style="display:none"></canvas>
    </div>
  </div>

  {{-- ABOUT BRAND --}}
  <div class="about-section">
    <h2>🏦 Về {{ $brand['name_short'] }}</h2>
    <p>{{ $brand['about'] }}</p>
  </div>

  {{-- FAQ --}}
  <section class="faq-section">
    <h2>❓ Câu hỏi thường gặp về giá bạc {{ $brand['name_short'] }}</h2>
    @foreach($brand['faqs'] as $faq)
    <div class="faq-item">
      <div class="faq-q">
        <span>{{ $faq['q'] }}</span>
        <span class="faq-caret">▼</span>
      </div>
      <div class="faq-a">{{ $faq['a'] }}</div>
    </div>
    @endforeach
  </section>

  {{-- RELATED BRANDS --}}
  <div class="related-section">
    <h2>Xem giá bạc thương hiệu khác</h2>
    <div class="related-grid">
      @if($brand['key'] !== 'phuquy')
      <a href="/gia-bac-phu-quy" class="related-card">
        <div class="related-icon" style="background:linear-gradient(135deg,#b0bec5,#546e7a)">🥈</div>
        <div><div class="related-name">Phú Quý 999</div><div class="related-sub">giá bạc phú quý</div></div>
      </a>
      @endif
      @if($brand['key'] !== 'ancarat')
      <a href="/gia-bac-ancarat" class="related-card">
        <div class="related-icon" style="background:linear-gradient(135deg,#06b6d4,#0284c7)">🏅</div>
        <div><div class="related-name">Ancarat 999</div><div class="related-sub">giá bạc ancarat</div></div>
      </a>
      @endif
      @if($brand['key'] !== 'doji')
      <a href="/gia-bac-doji" class="related-card">
        <div class="related-icon" style="background:linear-gradient(135deg,#dc2626,#991b1b)">🔴</div>
        <div><div class="related-name">DOJI 99.9</div><div class="related-sub">giá bạc doji</div></div>
      </a>
      @endif
      @if($brand['key'] !== 'kimnganphuc')
      <a href="/gia-bac-kim-ngan-phuc" class="related-card">
        <div class="related-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);font-size:10px;font-weight:900;color:#fff">KNP</div>
        <div><div class="related-name">Kim Ngân Phúc 999</div><div class="related-sub">giá bạc kim ngân phúc</div></div>
      </a>
      @endif
    </div>
  </div>

</main>

@include('frontend.partials.footer')

<script>
(function () {
  var JS_COMPUTED = @json($brand['js_computed'] ?? []);
  var BRAND   = '{{ $brand['key'] }}';
  var API_KEY = '{{ $brand['api'] }}';
  var API_URLS = {
    current: '/api/' + API_KEY + '/current',
    history: '/api/' + API_KEY + '/history',
  };

  var activeUnitBtn = document.querySelector('.unit-btn.active');
  var activeUnit  = activeUnitBtn ? activeUnitBtn.dataset.unit : '{{ $brand['default_unit'] }}';
  var activeMult  = activeUnitBtn ? (parseInt(activeUnitBtn.dataset.mult) || 1) : 1;
  var activeDays  = 7;
  var brandChart  = null;

  var UNIT_LABELS = @json($brand['unit_labels']);

  var fmt = function(n) { return Number(n).toLocaleString('vi-VN'); };

  /* ── Update SSR table with live data ── */
  function refreshPrices() {
    fetch(API_URLS.current)
      .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
      .then(function(json) {
        if (!json.success || !json.data) return;
        Object.keys(json.data).forEach(function(unit) {
          var d = json.data[unit];
          var buyEl    = document.getElementById('ssr-buy-'    + unit);
          var sellEl   = document.getElementById('ssr-sell-'   + unit);
          var spreadEl = document.getElementById('ssr-spread-' + unit);
          if (buyEl)    buyEl.textContent    = fmt(d.buy_price);
          if (sellEl)   sellEl.textContent   = fmt(d.sell_price);
          if (spreadEl) spreadEl.textContent = fmt(d.sell_price - d.buy_price);
        });
        // Handle computed units (e.g. LUONG_5 = LUONG × 5)
        Object.keys(JS_COMPUTED).forEach(function(targetKey) {
          var cfg = JS_COMPUTED[targetKey];
          if (!json.data[cfg.from]) return;
          var d = json.data[cfg.from];
          var buyEl    = document.getElementById('ssr-buy-'  + targetKey);
          var sellEl   = document.getElementById('ssr-sell-' + targetKey);
          var spEl     = document.getElementById('ssr-spread-' + targetKey);
          if (buyEl)  buyEl.textContent  = fmt(d.buy_price  * cfg.mult);
          if (sellEl) sellEl.textContent = fmt(d.sell_price * cfg.mult);
          if (spEl)   spEl.textContent   = fmt((d.sell_price - d.buy_price) * cfg.mult);
        });
        if (json.updated_at) {
          var el = document.getElementById('tbl-updated');
          if (el) el.textContent = 'Cập nhật: ' + json.updated_at;
        }
      })
      .catch(function(){});
  }

  /* ── Load chart ── */
  function loadChart() {
    var loading = document.getElementById('chart-loading');
    var canvas  = document.getElementById('brandChart');
    loading.style.display = 'flex'; canvas.style.display = 'none';

    var url = API_URLS.history + '?days=' + activeDays + '&type=' + activeUnit;
    fetch(url)
      .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
      .then(function(json) {
        loading.style.display = 'none';
        if (!json.success || !json.data || json.data.dates.length === 0) {
          loading.innerHTML = '<span style="color:var(--muted);font-size:12px">⚠️ ' + (json.message || 'Chưa có dữ liệu lịch sử') + '</span>';
          loading.style.display = 'flex';
          console.info('[Chart] no data:', BRAND, activeUnit, activeDays);
          return;
        }
        canvas.style.display = 'block';

        var buys  = json.data.buy_prices.map(function(v) { return v * activeMult; });
        var sells = json.data.sell_prices.map(function(v) { return v * activeMult; });
        var unitLabel = UNIT_LABELS[activeUnit] || activeUnit;
        if (activeMult > 1) unitLabel = activeMult + ' ' + unitLabel;

        if (brandChart) brandChart.destroy();

        /* ── Crosshair: đường dọc + ngang mờ khi hover ── */
        var crosshairPlugin = {
          id: 'brandCrosshair',
          afterDraw: function(chart) {
            if (chart._chX == null) return;
            var c = chart.ctx, y = chart.scales.y, x = chart.scales.x;
            c.save();
            c.setLineDash([4, 4]);
            c.lineWidth = 1;
            c.strokeStyle = 'rgba(255,255,255,0.18)';
            c.beginPath(); c.moveTo(chart._chX, y.top);   c.lineTo(chart._chX, y.bottom); c.stroke();
            c.beginPath(); c.moveTo(x.left, chart._chY);  c.lineTo(x.right,  chart._chY);  c.stroke();
            c.restore();
          }
        };

        brandChart = new Chart(canvas, {
          type: 'line',
          data: {
            labels: json.data.dates,
            datasets: [
              { label:'Giá bán ra',  data:sells, borderColor:'#4f7af8', backgroundColor:'rgba(79,122,248,0.07)',  borderWidth:2, pointRadius:json.data.dates.length<=15?3:1.5, pointHoverRadius:5, fill:true, tension:0.35 },
              { label:'Giá mua vào', data:buys,  borderColor:'#22c97a', backgroundColor:'rgba(34,201,122,0.06)',  borderWidth:2, pointRadius:json.data.dates.length<=15?3:1.5, pointHoverRadius:5, fill:true, tension:0.35 }
            ]
          },
          plugins: [crosshairPlugin],
          options: {
            responsive:true, maintainAspectRatio:false,
            interaction:{ mode:'index', intersect:false },
            onHover: function(event, _el, chart) {
              if (event.native) { chart._chX = event.x; chart._chY = event.y; }
            },
            plugins:{
              legend:{ labels:{ color:'#909ab2', font:{size:11,family:'Inter'}, usePointStyle:true, pointStyle:'line', pointStyleWidth:20, boxHeight:2 } },
              tooltip:{ backgroundColor:'rgba(13,16,24,0.97)', borderColor:'rgba(255,255,255,0.1)', borderWidth:1,
                titleColor:'#e4e8f2', bodyColor:'#909ab2', padding:10,
                callbacks:{ label:function(ctx){ return ' '+ctx.dataset.label+': '+Number(ctx.raw).toLocaleString('vi-VN')+' đ'; } }
              }
            },
            scales:{
              x:{ grid:{color:'rgba(255,255,255,0.04)'}, ticks:{color:'#6e778c',font:{size:10},maxTicksLimit:10} },
              y:{ grid:{color:'rgba(255,255,255,0.04)'},
                ticks:{color:'#6e778c',font:{size:10},callback:function(v){ return Number(v).toLocaleString('vi-VN'); }},
                title:{display:true,text:'VND/'+unitLabel,color:'#6e778c',font:{size:10}}
              }
            }
          }
        });

        canvas.onmouseleave = function() {
          if (brandChart) { brandChart._chX = null; brandChart._chY = null; brandChart.draw(); }
        };
      })
      .catch(function(err) {
        loading.style.display = 'flex';
        loading.innerHTML = '<span style="color:var(--muted);font-size:12px">⚠️ Chưa có dữ liệu lịch sử</span>';
        console.warn('[Chart] error:', err);
      });
  }

  /* ── Unit tab clicks ── */
  document.querySelectorAll('.unit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.unit-btn').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      activeUnit = btn.dataset.unit;
      activeMult = parseInt(btn.dataset.mult) || 1;
      loadChart();
    });
  });

  /* ── Period tab clicks ── */
  document.querySelectorAll('.prd-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.prd-btn').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      activeDays = parseInt(btn.dataset.days);
      loadChart();
    });
  });

  /* ── FAQ accordion ── */
  document.querySelectorAll('.faq-q').forEach(function(q) {
    q.addEventListener('click', function() {
      var item = q.closest('.faq-item');
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(function(i){ i.classList.remove('open'); });
      if (!isOpen) item.classList.add('open');
    });
  });

  /* ── Init ── */
  refreshPrices();
  loadChart();
  setInterval(refreshPrices, 30 * 60 * 1000);

})();
</script>
</body>
</html>
