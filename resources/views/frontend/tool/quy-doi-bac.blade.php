<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Quy Đổi Giá Bạc – Tính Toán KG, Lượng, Chỉ, Gram | GiáVàng.vn</title>
  <meta name="description" content="Công cụ quy đổi giá bạc trực tuyến: nhập số lượng, chọn đơn vị (KG, lượng, chỉ, gram) và thương hiệu để biết giá mua vào bán ra ngay lập tức."/>
  <link rel="canonical" href="{{ url('/quy-doi-bac') }}"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#07090f;--bg2:#0d1018;--bg3:#131724;--bg4:#1a2030;
      --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.12);
      --gold:#f5c518;--text:#e4e8f2;--text2:#c4cad8;--muted:#6e778c;--muted2:#909ab2;
      --green:#22c97a;--red:#f55252;--blue:#4f7af8;
      --radius:14px;--radius-sm:8px;
    }
    html{scroll-behavior:smooth}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    .bg-glow{position:fixed;inset:0;pointer-events:none;z-index:0;
      background:radial-gradient(ellipse 60% 40% at 20% 0%,rgba(79,122,248,0.07) 0%,transparent 60%),
                 radial-gradient(ellipse 50% 35% at 80% 90%,rgba(176,190,197,0.05) 0%,transparent 60%)}



    /* MAIN */
    main{position:relative;z-index:1;max-width:900px;margin:0 auto;padding:40px 24px 80px}
    @media(max-width:640px){
      main{padding:12px 0 60px}
      .breadcrumb{padding:0 12px}
      .page-head{padding:0 12px}
      .calc-card{border-radius:0;border-left:none;border-right:none}
      .info-card{border-radius:0;border-left:none;border-right:none}
      .result-wrap{border-radius:0}
    }

    /* BREADCRUMB */
    .breadcrumb{font-size:12px;color:var(--muted);margin-bottom:20px;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .breadcrumb a{color:var(--muted);text-decoration:none}.breadcrumb a:hover{color:var(--text2)}

    /* PAGE HEADER */
    .page-head{margin-bottom:32px}
    .page-head-row{display:flex;align-items:center;gap:14px;margin-bottom:8px}
    .page-head-icon{width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#4f7af8,#7c3aed);
      display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 4px 20px rgba(79,122,248,0.35)}
    .page-head h1{font-size:26px;font-weight:800;letter-spacing:-.4px}
    .page-head .sub{font-size:13px;color:var(--muted);margin-top:5px}
    @media(max-width:640px){.page-head h1{font-size:20px}}

    /* CALCULATOR CARD */
    .calc-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);
      padding:28px;margin-bottom:28px;border-top:2px solid #4f7af8}
    .calc-card h2{font-size:15px;font-weight:700;color:var(--text2);margin-bottom:20px;
      display:flex;align-items:center;gap:8px}

    .calc-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
    @media(max-width:560px){.calc-grid{grid-template-columns:1fr}}

    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group label{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
    .form-group input,.form-group select{
      background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius-sm);
      padding:10px 14px;color:var(--text);font-size:15px;font-family:'Inter',sans-serif;
      outline:none;transition:border-color .18s;width:100%}
    .form-group input:focus,.form-group select:focus{border-color:#4f7af8}
    .form-group input{font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:600}
    .form-group select option{background:var(--bg3)}

    /* BRAND SELECTOR */
    .brand-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin-bottom:20px}
    .brand-btn{
      background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius-sm);
      padding:12px 14px;cursor:pointer;transition:all .18s;display:flex;align-items:center;gap:10px;
      font-family:'Inter',sans-serif;color:var(--muted2)
    }
    .brand-btn:hover{border-color:var(--border2);color:var(--text)}
    .brand-btn.active{border-color:#4f7af8;background:rgba(79,122,248,0.12);color:var(--text)}
    .brand-btn-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;flex-shrink:0}
    .brand-btn-text{font-size:13px;font-weight:600;text-align:left;line-height:1.3}

    /* RESULT */
    .result-wrap{background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius-sm);padding:20px}
    .result-title{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px}
    .result-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .result-box{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px}
    .result-box-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;color:var(--muted)}
    .result-buy{color:var(--red);font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700}
    .result-sell{color:var(--green);font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700}
    .result-sub{font-size:11px;color:var(--muted);margin-top:4px}
    .result-note{font-size:12px;color:var(--muted);margin-top:12px;text-align:center}

    /* INFO SECTION */
    .info-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);
      padding:24px;margin-bottom:24px}
    .info-card h2{font-size:16px;font-weight:700;margin-bottom:12px;color:var(--text2)}
    .info-card p{font-size:14px;line-height:1.75;color:var(--muted2);margin-bottom:8px}
    .convert-table{width:100%;border-collapse:collapse;margin-top:12px}
    .convert-table th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
      color:var(--muted);padding:8px 12px;background:var(--bg3);border-bottom:1px solid var(--border);text-align:left}
    .convert-table td{padding:10px 12px;font-size:13px;border-bottom:1px solid var(--border);color:var(--text2)}
    .convert-table tr:last-child td{border-bottom:none}
    .convert-table td:nth-child(2){font-family:'JetBrains Mono',monospace;color:var(--blue)}

  </style>
</head>
<body>
<div class="bg-glow"></div>

@include('frontend.partials.header', ['activePage' => 'quydoi'])

<main>
  <div class="breadcrumb">
    <a href="/">Trang chủ</a><span>›</span><span>Quy Đổi Giá Bạc</span>
  </div>

  <div class="page-head">
    <div class="page-head-row">
      <div class="page-head-icon">⚖️</div>
      <h1>Quy Đổi Giá Bạc</h1>
    </div>
    <p class="sub">Nhập số lượng, chọn đơn vị và thương hiệu để tính tổng tiền mua vào / bán ra ngay lập tức.</p>
  </div>

  {{-- CALCULATOR --}}
  <div class="calc-card">
    <h2>⚖️ Máy Tính Quy Đổi</h2>

    <div class="calc-grid">
      <div class="form-group">
        <label>Số lượng</label>
        <input type="number" id="qty-input" value="1" min="0.001" step="any" placeholder="Nhập số lượng..."/>
      </div>
      <div class="form-group">
        <label>Đơn vị</label>
        <select id="unit-select">
          <option value="LUONG" data-gram="37.5">Lượng (37.5g)</option>
          <option value="CHI"   data-gram="3.75">Chỉ (3.75g)</option>
          <option value="KG"    data-gram="1000">Kilogram (1000g)</option>
          <option value="GRAM"  data-gram="1">Gram (1g)</option>
        </select>
      </div>
    </div>

    {{-- Brand selector --}}
    <div style="margin-bottom:14px">
      <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:8px">Thương hiệu</label>
      <div class="brand-grid">
        @foreach($brands as $key => $brand)
        <button class="brand-btn {{ $loop->first ? 'active' : '' }}" data-brand="{{ $key }}"
                onclick="selectBrand(this,'{{ $key }}')"
                style="{{ $loop->first ? 'border-color:'. $brand['color'] .';background:rgba(176,190,197,0.08)' : '' }}">
          <div class="brand-btn-icon" style="background:{{ $brand['gradient'] }};color:{{ strlen($brand['icon']) > 2 ? '#fff' : 'inherit' }};font-size:{{ strlen($brand['icon']) > 2 ? '9px' : '14px' }}">{{ $brand['icon'] }}</div>
          <div class="brand-btn-text">{{ $brand['name'] }}</div>
        </button>
        @endforeach
      </div>
    </div>

    {{-- Result --}}
    <div class="result-wrap">
      <div class="result-title">Kết quả tính toán</div>
      <div class="result-grid">
        <div class="result-box">
          <div class="result-box-label">💸 Giá mua vào (bạn bán)</div>
          <div class="result-buy" id="result-buy">–</div>
          <div class="result-sub" id="result-buy-sub"></div>
        </div>
        <div class="result-box">
          <div class="result-box-label">🏷️ Giá bán ra (bạn mua)</div>
          <div class="result-sell" id="result-sell">–</div>
          <div class="result-sub" id="result-sell-sub"></div>
        </div>
      </div>
      <div class="result-note" id="result-note">Đang tải giá từ thị trường...</div>
    </div>
  </div>

  {{-- Unit conversion table --}}
  <div class="info-card">
    <h2>📐 Bảng Quy Đổi Đơn Vị Bạc</h2>
    <p>Các đơn vị đo lường phổ biến khi giao dịch bạc tại Việt Nam:</p>
    <table class="convert-table">
      <thead><tr><th>Đơn vị</th><th>Quy đổi sang Gram</th><th>Ghi chú</th></tr></thead>
      <tbody>
        <tr><td>1 Lượng</td><td>37.5 gram</td><td>Đơn vị phổ biến nhất tại VN</td></tr>
        <tr><td>1 Chỉ</td><td>3.75 gram</td><td>= 1/10 lượng</td></tr>
        <tr><td>1 Kilogram (KG)</td><td>1,000 gram</td><td>= 26.67 lượng</td></tr>
        <tr><td>1 Tael (Lạng TQ)</td><td>50 gram</td><td>Thị trường quốc tế</td></tr>
        <tr><td>1 Troy Oz (XAG)</td><td>31.1 gram</td><td>Chuẩn quốc tế</td></tr>
      </tbody>
    </table>
  </div>

  {{-- SEO text --}}
  <div class="info-card">
    <h2>💡 Hướng Dẫn Quy Đổi Giá Bạc</h2>
    <p>Công cụ quy đổi giá bạc của GiáVàng.vn giúp bạn tính toán nhanh chóng giá trị mua vào và bán ra của bạc theo nhiều đơn vị khác nhau. Giá được lấy trực tiếp từ các thương hiệu bạc uy tín như Phú Quý, Ancarat, DOJI và Kim Ngân Phúc.</p>
    <p>Lưu ý: Giá hiển thị là giá tham khảo, cập nhật real-time. Hãy xác nhận giá chính thức từ thương hiệu trước khi giao dịch.</p>
  </div>
</main>

@include('frontend.partials.footer')

<script>
(function() {
  // SSR prices from PHP
  var PRICES = @json($prices);

  var activeBrand = 'phuquy';

  // gram per unit
  var GRAM_PER_UNIT = { LUONG: 37.5, CHI: 3.75, KG: 1000, GRAM: 1 };

  var qtyInput   = document.getElementById('qty-input');
  var unitSelect = document.getElementById('unit-select');
  var buyEl      = document.getElementById('result-buy');
  var sellEl     = document.getElementById('result-sell');
  var buySubEl   = document.getElementById('result-buy-sub');
  var sellSubEl  = document.getElementById('result-sell-sub');
  var noteEl     = document.getElementById('result-note');

  var fmt = function(n) { return Number(Math.round(n)).toLocaleString('vi-VN') + ' đ'; };

  function getBasePrice(brand, unit) {
    var p = PRICES[brand];
    if (!p) return null;
    // Try to get KG unit price first (most reliable)
    if (p['KG'])    return { buy: p['KG'].buy,    sell: p['KG'].sell,    gramsPer: 1000, label: 'KG' };
    if (p['LUONG']) return { buy: p['LUONG'].buy,  sell: p['LUONG'].sell, gramsPer: 37.5, label: 'Lượng' };
    return null;
  }

  function calculate() {
    var qty  = parseFloat(qtyInput.value) || 0;
    var unit = unitSelect.value;
    var grams = qty * GRAM_PER_UNIT[unit];

    var base = getBasePrice(activeBrand);
    if (!base || qty <= 0) {
      buyEl.textContent  = '–';
      sellEl.textContent = '–';
      buySubEl.textContent = ''; sellSubEl.textContent = '';
      noteEl.textContent = 'Đang tải giá...';
      return;
    }

    // price per gram
    var buyPerGram  = base.buy  / base.gramsPer;
    var sellPerGram = base.sell / base.gramsPer;

    var totalBuy  = buyPerGram  * grams;
    var totalSell = sellPerGram * grams;

    buyEl.textContent  = fmt(totalBuy);
    sellEl.textContent = fmt(totalSell);
    buySubEl.textContent  = '~' + Number(buyPerGram.toFixed(0)).toLocaleString('vi-VN') + ' đ/gram';
    sellSubEl.textContent = '~' + Number(sellPerGram.toFixed(0)).toLocaleString('vi-VN') + ' đ/gram';
    noteEl.textContent = grams.toLocaleString('vi-VN') + ' gram · Giá ' + base.label + '/gram từ ' + activeBrand;
  }

  window.selectBrand = function(btn, brand) {
    document.querySelectorAll('.brand-btn').forEach(function(b) {
      b.classList.remove('active');
      b.style.borderColor = '';
      b.style.background  = '';
    });
    btn.classList.add('active');
    activeBrand = brand;
    // Fetch live price for selected brand
    fetchLivePrice(brand);
    calculate();
  };

  function fetchLivePrice(brand) {
    var apiMap = {
      phuquy: '/api/silver/current', ancarat: '/api/ancarat/current',
      doji: '/api/doji/current', kimnganphuc: '/api/kimnganphuc/current'
    };
    var url = apiMap[brand];
    if (!url) return;
    fetch(url).then(function(r){return r.json();}).then(function(json){
      if (!json.success || !json.data) return;
      PRICES[brand] = {};
      Object.keys(json.data).forEach(function(unit) {
        PRICES[brand][unit] = { buy: json.data[unit].buy_price, sell: json.data[unit].sell_price };
      });
      calculate();
    }).catch(function(){});
  }

  qtyInput.addEventListener('input', calculate);
  unitSelect.addEventListener('change', calculate);

  // Init: fetch live prices for phuquy
  fetchLivePrice('phuquy');
  calculate();
})();
</script>
</body>
</html>
