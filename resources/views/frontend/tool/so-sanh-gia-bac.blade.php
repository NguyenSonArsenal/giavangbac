<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>So Sánh Giá Bạc Các Thương Hiệu Hôm Nay {{ now()->format('d/m/Y') }} | GiáVàng.vn</title>
  <meta name="description" content="So sánh giá bạc mua vào bán ra của Phú Quý, Ancarat, DOJI, Kim Ngân Phúc hôm nay {{ now()->format('d/m/Y') }}. Bảng giá real-time cập nhật liên tục."/>
  <link rel="canonical" href="{{ url('/so-sanh-gia-bac') }}"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#07090f;--bg2:#0d1018;--bg3:#131724;
      --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.12);
      --gold:#f5c518;--text:#e4e8f2;--text2:#c4cad8;--muted:#6e778c;--muted2:#909ab2;
      --green:#22c97a;--red:#f55252;--blue:#4f7af8;
      --radius:14px;--radius-sm:8px;
    }
    html{scroll-behavior:smooth}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    .bg-glow{position:fixed;inset:0;pointer-events:none;z-index:0;
      background:radial-gradient(ellipse 60% 40% at 20% 0%,rgba(79,122,248,0.07) 0%,transparent 60%),
                 radial-gradient(ellipse 50% 35% at 80% 90%,rgba(34,201,122,0.05) 0%,transparent 60%)}

    main{position:relative;z-index:1;max-width:1000px;margin:0 auto;padding:40px 24px 80px}
    @media(max-width:640px){
      main{padding:12px 0 60px}
      .breadcrumb{padding:0 12px}
      .page-head{padding:0 12px}
      .filter-bar{padding:0 12px}
      .cmp-wrap{border-radius:0;border-left:none;border-right:none}
      .info-card{border-radius:0;border-left:none;border-right:none}
    }
    .breadcrumb{font-size:12px;color:var(--muted);margin-bottom:20px;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .breadcrumb a{color:var(--muted);text-decoration:none}.breadcrumb a:hover{color:var(--text2)}
    .page-head{margin-bottom:32px}
    .page-head-row{display:flex;align-items:center;gap:14px;margin-bottom:8px}
    .page-head-icon{width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#22c97a,#059669);
      display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 4px 20px rgba(34,201,122,0.3)}
    .page-head h1{font-size:26px;font-weight:800;letter-spacing:-.4px}
    .page-head .sub{font-size:13px;color:var(--muted);margin-top:5px}

    /* FILTER BAR */
    .filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap}
    .filter-label{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
    .unit-tab{padding:6px 16px;border-radius:var(--radius-sm);font-size:12.5px;font-weight:600;
      border:1px solid var(--border);background:var(--bg3);color:var(--muted2);cursor:pointer;
      transition:all .18s;font-family:'Inter',sans-serif}
    .unit-tab:hover{border-color:var(--border2);color:var(--text)}
    .unit-tab.active{background:linear-gradient(135deg,#22c97a,#059669);color:#fff;border-color:transparent}
    .live-badge{margin-left:auto;display:flex;align-items:center;gap:6px;font-size:11px;color:var(--muted)}
    .live-dot{width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);
      animation:blink 1.3s infinite}
    @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

    /* COMPARISON TABLE */
    .cmp-wrap{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);
      overflow:hidden;margin-bottom:28px;box-shadow:0 8px 40px rgba(0,0,0,0.4)}
    .cmp-table{width:100%;border-collapse:collapse}
    .cmp-table thead th{padding:12px 20px;font-size:11px;font-weight:700;text-transform:uppercase;
      letter-spacing:.07em;color:var(--muted);background:var(--bg3);text-align:left;
      border-bottom:1px solid var(--border)}
    .cmp-table tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
    .cmp-table tbody tr:last-child{border-bottom:none}
    .cmp-table tbody tr:hover{background:rgba(255,255,255,0.02)}
    .cmp-table td{padding:16px 20px;vertical-align:middle}

    .cmp-brand-cell{display:flex;align-items:center;gap:10px}
    .cmp-brand-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;
      justify-content:center;flex-shrink:0;font-size:11px;font-weight:900}
    .cmp-brand-name{font-size:14px;font-weight:600;color:var(--text2)}
    .cmp-unit-badge{font-size:10px;color:var(--muted);margin-top:2px;font-weight:500}

    .cmp-price{font-family:'JetBrains Mono',monospace;font-size:17px;font-weight:700}
    .cmp-buy{color:var(--red)}.cmp-sell{color:var(--green)}
    .cmp-spread{font-size:13px;font-family:'JetBrains Mono',monospace;color:var(--muted2)}
    .cmp-time{font-size:11px;color:var(--muted)}
    .best-badge{display:inline-block;background:rgba(34,201,122,0.15);color:var(--green);
      font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;margin-left:6px;border:1px solid rgba(34,201,122,0.25)}
    .low-badge{display:inline-block;background:rgba(245,82,82,0.12);color:var(--red);
      font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;margin-left:6px;border:1px solid rgba(245,82,82,0.2)}

    /* CARDS mobile */
    @media(max-width:640px){
      .cmp-table thead{display:none}
      .cmp-table,
      .cmp-table tbody,
      .cmp-table tr,
      .cmp-table td{display:block;width:100%}
      .cmp-table tr{padding:14px 16px;border-bottom:1px solid var(--border)}
      .cmp-table td{padding:4px 0;display:flex;justify-content:space-between;align-items:center}
      .cmp-table td::before{content:attr(data-label);font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em}
    }

    /* INFO */
    .info-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);
      padding:24px;margin-bottom:24px}
    .info-card h2{font-size:16px;font-weight:700;margin-bottom:12px;color:var(--text2)}
    .info-card p{font-size:14px;line-height:1.75;color:var(--muted2);margin-bottom:8px}
    footer{border-top:1px solid var(--border);padding:20px 24px;text-align:center;font-size:12px;color:var(--muted)}
    footer a{color:var(--muted2);text-decoration:none}footer a:hover{color:var(--text)}
  </style>
</head>
<body>
<div class="bg-glow"></div>

@include('frontend.partials.header', ['activePage' => 'sosanh'])

<main>
  <div class="breadcrumb">
    <a href="/">Trang chủ</a><span>›</span><span>So Sánh Giá Bạc</span>
  </div>

  <div class="page-head">
    <div class="page-head-row">
      <div class="page-head-icon">📊</div>
      <h1>So Sánh Giá Bạc Hôm Nay – {{ now()->format('d/m/Y') }}</h1>
    </div>
    <p class="sub">Bảng so sánh giá bạc mua vào bán ra của tất cả thương hiệu · Cập nhật real-time</p>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <span class="filter-label">Đơn vị:</span>
    <button class="unit-tab active" data-unit="LUONG" onclick="filterUnit(this,'LUONG')">Lượng</button>
    <button class="unit-tab" data-unit="KG" onclick="filterUnit(this,'KG')">Kilogram</button>
    <div class="live-badge"><span class="live-dot"></span> Đang cập nhật</div>
  </div>

  {{-- COMPARISON TABLE --}}
  <div class="cmp-wrap">
    <table class="cmp-table" id="cmp-table">
      <thead>
        <tr>
          <th>Thương hiệu / Đơn vị</th>
          <th>Giá Mua Vào ↑</th>
          <th>Giá Bán Ra ↓</th>
          <th>Chênh Lệch</th>
          <th>Cập nhật</th>
        </tr>
      </thead>
      <tbody id="cmp-tbody">
        @foreach($rows as $row)
        <tr class="cmp-row" data-unit="{{ $row['unit'] }}" data-brand="{{ $row['brand_key'] }}"
            style="{{ $row['unit'] === 'LUONG' ? '' : 'display:none' }}">
          <td data-label="Thương hiệu">
            <div class="cmp-brand-cell">
              <div class="cmp-brand-icon" style="background:{{ $brands[$row['brand_key']]['gradient'] }};color:{{ strlen($row['brand_icon']) > 2 ? '#fff' : 'inherit' }};font-size:{{ strlen($row['brand_icon']) > 2 ? '9px' : '15px' }}">{{ $row['brand_icon'] }}</div>
              <div>
                <div class="cmp-brand-name"><a href="/gia-bac-{{ str_replace(['phuquy','ancarat','doji','kimnganphuc'],['phu-quy','ancarat','doji','kim-ngan-phuc'],$row['brand_key']) }}" style="color:inherit;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.1)">{{ $row['brand_name'] }}</a></div>
                <div class="cmp-unit-badge">{{ $row['unit_label'] }}</div>
              </div>
            </div>
          </td>
          <td data-label="Mua vào">
            <span class="cmp-price cmp-buy" id="cmp-buy-{{ $row['brand_key'] }}-{{ $row['unit'] }}">{{ number_format($row['buy']) }}</span>
          </td>
          <td data-label="Bán ra">
            <span class="cmp-price cmp-sell" id="cmp-sell-{{ $row['brand_key'] }}-{{ $row['unit'] }}">{{ number_format($row['sell']) }}</span>
          </td>
          <td data-label="Chênh lệch">
            <span class="cmp-spread" id="cmp-spread-{{ $row['brand_key'] }}-{{ $row['unit'] }}">{{ number_format($row['spread']) }}</span>
          </td>
          <td data-label="Cập nhật">
            <span class="cmp-time" id="cmp-time-{{ $row['brand_key'] }}">{{ $row['updated_at'] ?? '–' }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- SEO INFO --}}
  <div class="info-card">
    <h2>📌 Hướng Dẫn Đọc Bảng So Sánh</h2>
    <p><strong>Giá mua vào</strong>: Giá thương hiệu mua bạc từ bạn (bạn bán bạc và nhận tiền). Thường thấp hơn giá bán ra.</p>
    <p><strong>Giá bán ra</strong>: Giá thương hiệu bán bạc cho bạn (bạn mua bạc và trả tiền). Thường cao hơn giá mua vào.</p>
    <p><strong>Chênh lệch (Spread)</strong>: Hiệu số giữa giá bán ra và giá mua vào. Spread thấp hơn nghĩa là chi phí giao dịch thấp hơn — có lợi hơn cho nhà đầu tư.</p>
    <p>Bảng giá được cập nhật tự động real-time trong giờ giao dịch từ các thương hiệu bạc uy tín tại Việt Nam.</p>
  </div>
</main>

<footer>
  <p>⚠️ Giá tham khảo · Xác nhận từ nguồn chính thức trước khi giao dịch</p>
  <p style="margin-top:6px">
    <a href="/">GiáVàng.vn</a> ·
    <a href="/quy-doi-bac">Quy Đổi</a> ·
    <a href="/so-sanh-gia-bac">So Sánh</a> ·
    <a href="/lich-su-gia-bac">Lịch Sử</a>
  </p>
  <p style="margin-top:6px">© {{ now()->year }} GiáVàng.vn</p>
</footer>

<script>
(function() {
  var API = {
    phuquy:      '/api/silver/current',
    ancarat:     '/api/ancarat/current',
    doji:        '/api/doji/current',
    kimnganphuc: '/api/kimnganphuc/current',
  };

  var fmt = function(n) { return Number(n).toLocaleString('vi-VN'); };

  function refreshAll() {
    Object.keys(API).forEach(function(brand) {
      fetch(API[brand]).then(function(r){return r.json();}).then(function(json){
        if (!json.success || !json.data) return;
        Object.keys(json.data).forEach(function(unit) {
          var d = json.data[unit];
          var buyEl    = document.getElementById('cmp-buy-'    + brand + '-' + unit);
          var sellEl   = document.getElementById('cmp-sell-'   + brand + '-' + unit);
          var spreadEl = document.getElementById('cmp-spread-' + brand + '-' + unit);
          var timeEl   = document.getElementById('cmp-time-'   + brand);
          if (buyEl)    buyEl.textContent    = fmt(d.buy_price);
          if (sellEl)   sellEl.textContent   = fmt(d.sell_price);
          if (spreadEl) spreadEl.textContent = fmt(d.sell_price - d.buy_price);
        });
        if (json.updated_at) {
          var t = document.getElementById('cmp-time-' + brand);
          if (t) t.textContent = json.updated_at;
        }
        // Highlight best buy/sell after all refresh
        highlightBest();
      }).catch(function(){});
    });
  }

  function highlightBest() {
    var activeUnit = document.querySelector('.unit-tab.active').dataset.unit;
    var rows = document.querySelectorAll('.cmp-row[data-unit="' + activeUnit + '"]');
    var sells = [], buys = [];
    rows.forEach(function(row) {
      var brand = row.dataset.brand;
      var s = document.getElementById('cmp-sell-' + brand + '-' + activeUnit);
      var b = document.getElementById('cmp-buy-'  + brand + '-' + activeUnit);
      if (s) sells.push({ el: s, val: parseInt(s.textContent.replace(/\D/g,'')) });
      if (b) buys.push({ el: b, val: parseInt(b.textContent.replace(/\D/g,'')) });
    });
    // Remove old badges
    document.querySelectorAll('.best-badge,.low-badge').forEach(function(b){b.remove()});
    if (sells.length > 1) {
      var minSell = sells.reduce(function(a,b){return a.val < b.val ? a : b});
      var maxSell = sells.reduce(function(a,b){return a.val > b.val ? a : b});
      var badge1 = document.createElement('span'); badge1.className='best-badge'; badge1.textContent='Thấp nhất';
      var badge2 = document.createElement('span'); badge2.className='low-badge';  badge2.textContent='Cao nhất';
      if (minSell.el !== maxSell.el) { minSell.el.appendChild(badge1); maxSell.el.appendChild(badge2); }
    }
  }

  window.filterUnit = function(btn, unit) {
    document.querySelectorAll('.unit-tab').forEach(function(b){b.classList.remove('active')});
    btn.classList.add('active');
    document.querySelectorAll('.cmp-row').forEach(function(row){
      row.style.display = (row.dataset.unit === unit) ? '' : 'none';
    });
    highlightBest();
  };

  refreshAll();
  setInterval(refreshAll, 30 * 60 * 1000);
})();
</script>
</body>
</html>
