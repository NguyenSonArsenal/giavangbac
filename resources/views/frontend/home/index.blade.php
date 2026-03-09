<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giá Vàng & Bạc Thế Giới – GiáVàng.vn</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:        #07090f;
      --bg2:       #0d1018;
      --bg3:       #131724;
      --bg4:       #1a2030;
      --border:    rgba(255,255,255,0.07);
      --border2:   rgba(255,255,255,0.12);
      --gold:      #f5c518;
      --gold2:     #ffd76e;
      --silver:    #b0bec5;
      --silver2:   #dde6ed;
      --silver3:   #60d4f0;
      --text:      #e4e8f2;
      --text2:     #c4cad8;
      --muted:     #6e778c;
      --muted2:    #909ab2;
      --green:     #22c97a;
      --red:       #f55252;
      --blue:      #4f7af8;
      --radius:    14px;
      --radius-sm: 8px;
      --shadow:    0 8px 40px rgba(0,0,0,0.55);
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

    /* Background glow */
    .bg-glow {
      position: fixed; inset: 0; pointer-events: none; z-index: 0;
      background:
        radial-gradient(ellipse 70% 45% at 15% 0%, rgba(79,122,248,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 85% 90%, rgba(245,197,24,0.05) 0%, transparent 60%);
    }

    /* HEADER */
    header {
      position: sticky; top: 0; z-index: 100;
      background: rgba(7,9,15,0.92);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
      padding: 0 32px; height: 60px;
      display: flex; align-items: center; gap: 24px;
    }

    .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .logo-icon {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--gold), #c8820a);
      border-radius: 50%; display: flex; align-items: center;
      justify-content: center; font-size: 16px; font-weight: 900;
      color: #07090f; box-shadow: 0 0 18px rgba(245,197,24,0.4);
    }
    .logo-text {
      font-size: 20px; font-weight: 800;
      background: linear-gradient(90deg, var(--gold2), var(--gold));
      -webkit-background-clip: text; background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .logo-text span { -webkit-text-fill-color: var(--muted); font-weight: 400; font-size: 13px; }

    .header-tag {
      margin-left: auto; font-size: 12px; color: var(--muted);
      display: flex; align-items: center; gap: 6px;
    }

    .live-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--green); box-shadow: 0 0 8px var(--green);
      animation: blink 1.3s infinite; flex-shrink: 0;
    }

    @keyframes blink {
      0%,100% { opacity:1; box-shadow: 0 0 8px var(--green); }
      50%      { opacity:0.3; box-shadow:none; }
    }

    /* MAIN */
    main {
      position: relative; z-index: 1;
      max-width: 1600px; margin: 0 auto;
      padding: 32px 28px 80px;
    }

    /* PAGE HEADER */
    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 24px; font-weight: 800; letter-spacing: -0.4px; margin-bottom: 5px; }
    .page-header p  { font-size: 13px; color: var(--muted); }

    /* TICKER */
    .ticker-wrap {
      background: var(--bg2);
      border: 1px solid rgba(245,197,24,0.15);
      border-radius: var(--radius); overflow: hidden; margin-bottom: 28px;
    }
    .ticker-label {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 16px 7px; font-size: 11px; font-weight: 700;
      color: var(--gold2); text-transform: uppercase; letter-spacing: 0.1em;
      background: linear-gradient(90deg, rgba(245,197,24,0.08), transparent);
      border-bottom: 1px solid rgba(245,197,24,0.08);
    }
    .tradingview-widget-container.ticker-tv { width: 100%; min-height: 46px; }

    /* SECTION */
    .section-block { margin-bottom: 44px; }
    .section-head {
      display: flex; align-items: center; gap: 14px;
      margin-bottom: 20px;
    }
    .section-icon {
      width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }
    .section-head h2 { font-size: 20px; font-weight: 800; letter-spacing: -0.3px; }
    .section-head p  { font-size: 12.5px; color: var(--muted); margin-top: 2px; }

    /* TV CHARTS GRID */
    .tv-charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media(max-width:960px) { .tv-charts-grid { grid-template-columns: 1fr; } }

    .tv-chart-card {
      border-radius: var(--radius); overflow: hidden;
      display: flex; flex-direction: column;
      border: 1px solid var(--border); background: var(--bg2);
      transition: border-color .25s, box-shadow .25s, transform .25s;
    }
    .tv-chart-card:hover { transform: translateY(-3px); }

    .card-gold   { border-color: rgba(245,197,24,0.22); box-shadow: 0 0 0 1px rgba(245,197,24,0.06); }
    .card-gold:hover { border-color: rgba(245,197,24,0.45); box-shadow: 0 10px 40px rgba(245,197,24,0.12); }
    .card-silver { border-color: rgba(176,190,197,0.22); box-shadow: 0 0 0 1px rgba(176,190,197,0.06); }
    .card-silver:hover { border-color: rgba(176,190,197,0.45); box-shadow: 0 10px 40px rgba(176,190,197,0.1); }

    .card-head {
      display: flex; align-items: center; gap: 14px;
      padding: 16px 20px; border-bottom: 1px solid var(--border);
    }
    .card-gold   .card-head { background: linear-gradient(90deg, rgba(245,197,24,0.1), transparent); border-bottom-color: rgba(245,197,24,0.1); }
    .card-silver .card-head { background: linear-gradient(90deg, rgba(176,190,197,0.08), transparent); border-bottom-color: rgba(176,190,197,0.1); }

    .card-badge {
      width: 44px; height: 44px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    .badge-gold   { background:linear-gradient(135deg,rgba(245,197,24,.25),rgba(245,197,24,.08)); border:1px solid rgba(245,197,24,.3); }
    .badge-silver { background:linear-gradient(135deg,rgba(176,190,197,.2),rgba(176,190,197,.06)); border:1px solid rgba(176,190,197,.25); }

    .card-info { flex: 1; }
    .card-name { font-size: 16px; font-weight: 800; }
    .card-gold  .card-name { color: var(--gold2); }
    .card-silver .card-name { color: var(--silver2); }
    .card-symbol { font-size: 11px; color: var(--muted); margin-top: 3px; font-family:'JetBrains Mono',monospace; letter-spacing:.03em; }

    .card-powered { text-align: right; flex-shrink: 0; }
    .card-powered span { display:block; font-size:9px; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; }
    .card-powered strong { font-size:12px; font-weight:700; color:var(--blue); }

    .card-chart { width:100%; height:600px; display:block; }
    .card-chart .tradingview-widget-container { width:100%; height:100%; }
    .card-chart .tradingview-widget-container__widget { width:100%; height:calc(100% - 32px); }

    /* ����������������������������������������������
       SILVER PRICE SECTION
    ���������������������������������������������� */
    .silver-section {
      background: var(--bg2);
      border: 1px solid rgba(176,190,197,0.18);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    /* Header */
    .sv-header {
      padding: 20px 24px 16px;
      background: linear-gradient(135deg, rgba(176,190,197,0.1), rgba(96,212,240,0.05), transparent);
      border-bottom: 1px solid rgba(176,190,197,0.12);
      display: flex; align-items: flex-start; justify-content: space-between;
      flex-wrap: wrap; gap: 16px;
    }

    .sv-title-row { display:flex; align-items:center; gap:12px; }
    .sv-title-icon { font-size: 28px; filter: drop-shadow(0 0 8px rgba(176,190,197,0.5)); }
    .sv-title-info h3 { font-size: 18px; font-weight: 800; color: var(--silver2); letter-spacing: -0.3px; }
    .sv-title-info p  { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* Unit tabs — dùng chung cho tất cả thương hiệu */
    .sv-unit-tabs { display: flex; gap: 6px; }
    .sv-unit-btn, .ac-unit-btn, .dj-unit-btn {
      padding: 7px 16px; border-radius: var(--radius-sm);
      font-size: 12.5px; font-weight: 600;
      border: 1px solid var(--border); background: var(--bg3);
      color: var(--muted2); cursor: pointer;
      transition: all .2s; font-family: 'Inter', sans-serif;
    }
    .sv-unit-btn:hover, .ac-unit-btn:hover, .dj-unit-btn:hover { border-color: var(--border2); color: var(--text); }
    .sv-unit-btn.active, .ac-unit-btn.active, .dj-unit-btn.active {
      background: linear-gradient(135deg, #b0bec5, #78909c);
      color: #07090f; border-color: transparent;
      box-shadow: 0 2px 10px rgba(176,190,197,0.35);
    }

    /* Price display */
    .sv-prices {
      display: flex; align-items: flex-start; gap: 32px;
      padding: 20px 24px 16px;
      border-bottom: 1px solid var(--border);
      flex-wrap: wrap;
    }

    .sv-price-block { display: flex; flex-direction: column; gap: 4px; }
    .sv-price-label { font-size: 12px; color: var(--muted); font-weight: 500; }
    .sv-price-buy {
      font-size: 32px; font-weight: 900;
      font-family: 'JetBrains Mono', monospace; letter-spacing: -1px;
      color: #f55252;
    }
    .sv-price-sell {
      font-size: 32px; font-weight: 900;
      font-family: 'JetBrains Mono', monospace; letter-spacing: -1px;
      color: var(--green);
    }

    .sv-percent-block {
      margin-left: auto; display: flex; align-items: center; gap: 10px;
      background: var(--bg3); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 10px 18px;
      flex-wrap: wrap;
    }
    .sv-pct-value {
      font-size: 22px; font-weight: 800;
      font-family: 'JetBrains Mono', monospace;
    }
    .sv-pct-value.up   { color: var(--green); }
    .sv-pct-value.down { color: var(--red); }
    .sv-pct-days  { font-size: 12px; color: var(--muted); }
    .sv-updated   { font-size: 11px; color: var(--muted); padding: 0 24px 12px; }

    /* Chart controls */
    .sv-chart-controls {
      display: flex; align-items: center; gap: 8px;
      padding: 16px 24px 0;
      flex-wrap: wrap;
    }
    .sv-chart-label { font-size: 13px; font-weight: 600; color: var(--silver2); margin-right: 4px; }
    /* Period buttons — dùng chung cho tất cả thương hiệu */
    .sv-period-btn, .ac-period-btn, .dj-period-btn {
      padding: 5px 13px; border-radius: 6px;
      font-size: 12px; font-weight: 600;
      border: 1px solid var(--border); background: var(--bg3);
      color: var(--muted2); cursor: pointer;
      transition: all .2s; font-family: 'Inter', sans-serif;
    }
    .sv-period-btn:hover, .ac-period-btn:hover, .dj-period-btn:hover { border-color: var(--border2); color: var(--text); }
    .sv-period-btn.active, .ac-period-btn.active, .dj-period-btn.active {
      background: linear-gradient(135deg, var(--blue), #6d28d9);
      color: #fff; border-color: transparent;
      box-shadow: 0 2px 8px rgba(79,122,248,0.35);
    }

    .sv-chart-type-label { font-size: 11px; color: var(--muted); margin-left: 4px; }

    /* Chart canvas wrapper */
    .sv-canvas-wrap {
      padding: 16px 24px 24px;
      position: relative; height: 420px;
    }
    /* Canvas — dùng chung cho tất cả chart */
    .sv-canvas-wrap canvas { width: 100% !important; height: 100% !important; }

    /* Loading / error state */
    .sv-loading {
      display: flex; align-items: center; justify-content: center;
      height: 200px; color: var(--muted); font-size: 14px; gap: 10px;
    }
    .sv-spinner {
      width: 20px; height: 20px; border: 2px solid var(--border2);
      border-top-color: var(--silver); border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* No-data */
    .sv-no-data {
      padding: 32px 24px; text-align: center;
      color: var(--muted); font-size: 13px;
    }
    .sv-no-data strong { color: var(--text2); display: block; margin-bottom: 6px; }

    /* Footer note */
    .foot-note { text-align:center; margin-top:32px; font-size:12px; color:var(--muted); }
  
    /* ══════════════════════════════════════════════
       COMPACT SILVER BRAND SECTION
    ══════════════════════════════════════════════ */
    .sv-compact-section {
      margin-top: 32px;
    }
    .sv-section-head {
      display: flex; align-items: center; gap: 14px;
      margin-bottom: 16px;
    }
    .sv-section-head .sv-section-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: linear-gradient(135deg,#b0bec5,#546e7a);
      display:flex; align-items:center; justify-content:center;
      font-size:20px; flex-shrink:0;
    }
    .sv-section-head h2 { font-size:17px; font-weight:800; color:#e4e8f2; margin:0; }
    .sv-section-head p  { font-size:11.5px; color:var(--muted); margin:2px 0 0; }

    /* Brand cards grid */
    .sv-brands-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
      margin-bottom: 14px;
    }
    @media (max-width:900px) {
      .sv-brands-grid { grid-template-columns:1fr; }
    }

    .sv-brand-card {
      background: var(--bg2);
      border: 1px solid rgba(176,190,197,0.15);
      border-radius: var(--radius);
      padding: 14px 16px 12px;
      cursor: pointer;
      transition: border-color .2s, box-shadow .2s;
    }
    .sv-brand-card:hover { border-color: rgba(176,190,197,0.35); }
    .sv-brand-card.active {
      border-color: var(--blue);
      box-shadow: 0 0 0 1px var(--blue), 0 4px 20px rgba(79,122,248,0.12);
    }

    /* Card header */
    .sv-card-head {
      display:flex; align-items:center; gap:10px; margin-bottom:10px;
    }
    .sv-card-logo {
      width:32px; height:32px; border-radius:8px;
      display:flex; align-items:center; justify-content:center;
      font-size:16px; flex-shrink:0;
    }
    .sv-card-name { font-size:13px; font-weight:700; color:var(--silver2); }
    .sv-card-sub  { font-size:10.5px; color:var(--muted); margin-top:1px; }

    /* Unit tabs inside card */
    .sv-card-tabs { display:flex; gap:5px; margin-bottom:10px; }
    .sv-tab {
      padding: 4px 10px; border-radius: 5px;
      font-size: 11.5px; font-weight: 600;
      border: 1px solid var(--border); background: var(--bg3);
      color: var(--muted2); cursor: pointer;
      transition: all .18s; font-family:'Inter',sans-serif;
    }
    .sv-tab:hover { border-color:var(--border2); color:var(--text); }
    .sv-tab.active {
      background: linear-gradient(135deg,#b0bec5,#78909c);
      color:#07090f; border-color:transparent;
      box-shadow: 0 2px 8px rgba(176,190,197,0.3);
    }

    /* Card prices */
    .sv-card-prices {
      display:flex; align-items:flex-end; gap:12px;
    }
    .sv-card-price-col { display:flex; flex-direction:column; gap:2px; }
    .sv-cprice-label   { font-size:10px; color:var(--muted); }
    .sv-cprice-buy  {
      font-size:20px; font-weight:900;
      font-family:'JetBrains Mono',monospace;
      color:#f55252; letter-spacing:-0.5px; line-height:1;
    }
    .sv-cprice-sell {
      font-size:20px; font-weight:900;
      font-family:'JetBrains Mono',monospace;
      color:var(--green); letter-spacing:-0.5px; line-height:1;
    }
    .sv-card-pct {
      margin-left:auto;
      font-size:16px; font-weight:800;
      font-family:'JetBrains Mono',monospace;
    }
    .sv-card-pct.up   { color:var(--green); }
    .sv-card-pct.down { color:var(--red); }
    .sv-card-pct-days { font-size:10px; color:var(--muted); text-align:right; margin-top:2px; }

    /* Shared chart section */
    .sv-shared-chart-wrap {
      background: var(--bg2);
      border: 1px solid rgba(176,190,197,0.15);
      border-radius: var(--radius);
      overflow:hidden;
    }
    .sv-shared-chart-bar {
      display:flex; align-items:center; gap:10px;
      padding: 12px 16px 10px;
      border-bottom: 1px solid var(--border);
      flex-wrap:wrap;
    }
    .sv-chart-brand-tabs { display:flex; gap:5px; }
    .sv-chart-brand {
      padding: 5px 13px; border-radius:6px;
      font-size:12px; font-weight:600;
      border:1px solid var(--border); background:var(--bg3);
      color:var(--muted2); cursor:pointer;
      transition:all .18s; font-family:'Inter',sans-serif;
    }
    .sv-chart-brand:hover { border-color:var(--border2); color:var(--text); }
    .sv-chart-brand.active {
      background:linear-gradient(135deg,var(--blue),#6d28d9);
      color:#fff; border-color:transparent;
      box-shadow:0 2px 8px rgba(79,122,248,0.35);
    }
    .sv-chart-period-tabs { display:flex; gap:5px; margin-left:4px; }
    .sv-prd {
      padding: 5px 11px; border-radius:6px;
      font-size:11.5px; font-weight:600;
      border:1px solid var(--border); background:var(--bg3);
      color:var(--muted2); cursor:pointer;
      transition:all .18s; font-family:'Inter',sans-serif;
    }
    .sv-prd:hover { border-color:var(--border2); color:var(--text); }
    .sv-prd.active {
      background:rgba(79,122,248,0.18);
      color:var(--blue); border-color:rgba(79,122,248,0.4);
    }
    .sv-chart-unit-label {
      margin-left:auto; font-size:11px; color:var(--muted);
    }
    .sv-shared-canvas-wrap {
      padding:12px 16px 16px;
      position:relative; height:320px;
    }
    .sv-shared-canvas-wrap canvas { width:100%!important; height:100%!important; }

    .sv-footnote {
      font-size:10.5px; color:var(--muted);
      text-align:center; margin-top:10px; padding:0 4px;
    }

  
    /* ══ WORLD CHARTS SECTION ══ */
    .world-charts-section {
      margin-bottom: 28px;
    }
    .wc-header {
      display:flex; align-items:center; gap:14px; margin-bottom:14px;
    }
    .wc-header-icon {
      width:40px; height:40px; border-radius:10px;
      background:linear-gradient(135deg,#f59e0b,#d97706);
      display:flex; align-items:center; justify-content:center;
      font-size:20px; flex-shrink:0;
    }
    .wc-header h2 { font-size:17px; font-weight:800; color:#e4e8f2; margin:0; }
    .wc-header p  { font-size:11.5px; color:var(--muted); margin:2px 0 0; }

    .world-charts-grid {
      display:grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }
    @media (max-width:700px) {
      .world-charts-grid { grid-template-columns:1fr; }
    }

    .world-chart-card {
      background: var(--bg2);
      border: 1px solid rgba(176,190,197,0.15);
      border-radius: var(--radius);
      padding: 12px 14px 10px;
      overflow: hidden;
    }
    .wc-card-label {
      display:flex; align-items:center; gap:7px;
      font-size:12px; font-weight:700; color:var(--silver2);
      margin-bottom:10px;
    }
    .wc-dot {
      width:8px; height:8px; border-radius:50%; flex-shrink:0;
    }
    .wc-widget-wrap {
      height: 220px;
      border-radius: 8px;
      overflow: hidden;
    }
    .wc-widget-wrap .tradingview-widget-container,
    .wc-widget-wrap .tradingview-widget-container__widget {
      width:100%; height:100%;
    }

  </style>
</head>
<body>
<div class="bg-glow"></div>

<!-- HEADER -->
<header>
  <a href="/" class="logo">
    <div class="logo-icon">G</div>
    <div class="logo-text">GiáVàng<span>.vn</span></div>
  </a>
  <div class="header-tag">
    <span class="live-dot"></span>
    Dữ liệu trực tiếp · Cập nhật realtime
  </div>
</header>

<main>

  <!-- Page title -->
  <div class="page-header">
    <h1>📊 Giá Vàng & Bạc – Tổng Hợp</h1>
    <p>Biểu đồ realtime thế giới · Giá bạc Phú Quý cập nhật mỗi 30 phút</p>
  </div>

  <!-- Ticker Tape -->
  <div class="ticker-wrap">
    <div class="ticker-label">
      <span class="live-dot"></span>
      Giá Thế Giới Trực Tiếp
    </div>
    <div class="tradingview-widget-container ticker-tv">
      <script src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js">
        {
          "symbols": [
            {"proName": "OANDA:XAUUSD",  "title": "Vàng – Gold"},
            {"proName": "OANDA:XAGUSD",  "title": "Bạc – Silver"},
            {"proName": "FX_IDC:USDVND", "title": "USD/VND"},
            {"proName": "COMEX:GC1!",    "title": "Gold Futures"},
            {"proName": "COMEX:SI1!",    "title": "Silver Futures"}
          ],
          "showSymbolLogo": true,
          "colorTheme": "dark",
          "isTransparent": true,
          "displayMode": "adaptive",
          "locale": "vi"
        }
      </script>
    </div>
  </div>

  <!-- ── SECTION 1: TradingView Live Charts ───────────── -->
  

    <!-- ══ SECTION: Giá Vàng & Bạc Thế Giới ══ -->
  <section class="world-charts-section">
    <div class="wc-header">
      <div class="wc-header-icon">🌍</div>
      <div>
        <h2>Giá Vàng & Bạc Thế Giới</h2>
        <p>Biểu đồ realtime từ TradingView · Đơn vị USD/oz</p>
      </div>
    </div>
    <div class="world-charts-grid">
      <!-- Gold XAU/USD -->
      <div class="world-chart-card">
        <div class="wc-card-label">
          <span class="wc-dot" style="background:#f59e0b"></span> Vàng – XAU/USD
        </div>
        <div class="wc-widget-wrap">
          <div class="tradingview-widget-container" id="tv-gold">
            <div class="tradingview-widget-container__widget"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
            {
              "symbol": "OANDA:XAUUSD",
              "width": "100%",
              "height": "100%",
              "locale": "vi",
              "dateRange": "1D",
              "colorTheme": "dark",
              "isTransparent": true,
              "autosize": true,
              "largeChartUrl": "",
              "noTimeScale": false
            }
            </script>
          </div>
        </div>
      </div>
      <!-- Silver XAG/USD -->
      <div class="world-chart-card">
        <div class="wc-card-label">
          <span class="wc-dot" style="background:#94a3b8"></span> Bạc – XAG/USD
        </div>
        <div class="wc-widget-wrap">
          <div class="tradingview-widget-container" id="tv-silver">
            <div class="tradingview-widget-container__widget"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
            {
              "symbol": "OANDA:XAGUSD",
              "width": "100%",
              "height": "100%",
              "locale": "vi",
              "dateRange": "1D",
              "colorTheme": "dark",
              "isTransparent": true,
              "autosize": true,
              "largeChartUrl": "",
              "noTimeScale": false
            }
            </script>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ BẠC THƯƠNG HIỆU: Compact 3-column ══ -->
  <section class="sv-compact-section">

    <div class="sv-section-head">
      <div class="sv-section-icon">🥈</div>
      <div>
        <h2>Giá Bạc Thương Hiệu</h2>
        <p>Phú Quý · Ancarat · DOJI · Cập nhật mỗi 30 phút</p>
      </div>
    </div>

    <!-- 4 Brand Cards -->
    <div class="sv-brands-grid">

      <!-- Phú Quý -->
      <div class="sv-brand-card" id="sv-card-phuquy" data-brand="phuquy">
        <div class="sv-card-head">
          <div class="sv-card-logo" style="background:linear-gradient(135deg,#b0bec5,#546e7a)">🥈</div>
          <div class="sv-card-info">
            <div class="sv-card-name">Phú Quý 999</div>
            <div class="sv-card-sub" id="pq-updated">Đang tải...</div>
          </div>
        </div>
        <div class="sv-card-tabs">
          <button class="sv-tab active" data-brand="phuquy" data-unit="CHI" data-mult="1">Chỉ</button>
          <button class="sv-tab" data-brand="phuquy" data-unit="LUONG" data-mult="1">Lượng</button>
          <button class="sv-tab" data-brand="phuquy" data-unit="KG" data-mult="1">KG</button>
        </div>
        <div class="sv-card-prices">
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Mua vào</div>
            <div class="sv-cprice-buy" id="pq-buy">–</div>
          </div>
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Bán ra</div>
            <div class="sv-cprice-sell" id="pq-sell">–</div>
          </div>
          <div class="sv-card-pct" id="pq-pct">–</div>
        </div>
        <div class="sv-card-pct-days" id="pq-pct-days">7 ngày</div>
      </div>

      <!-- Ancarat -->
      <div class="sv-brand-card" id="sv-card-ancarat" data-brand="ancarat">
        <div class="sv-card-head">
          <div class="sv-card-logo" style="background:linear-gradient(135deg,#06b6d4,#0284c7)">🏅</div>
          <div class="sv-card-info">
            <div class="sv-card-name">Bạc 999 – Ancarat</div>
            <div class="sv-card-sub" id="ac-updated">Đang tải...</div>
          </div>
        </div>
        <div class="sv-card-tabs">
          <button class="sv-tab active" data-brand="ancarat" data-unit="LUONG" data-mult="1">Lượng</button>
          <button class="sv-tab" data-brand="ancarat" data-unit="KG" data-mult="1">KG</button>
        </div>
        <div class="sv-card-prices">
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Mua vào</div>
            <div class="sv-cprice-buy" id="ac-buy">–</div>
          </div>
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Bán ra</div>
            <div class="sv-cprice-sell" id="ac-sell">–</div>
          </div>
          <div class="sv-card-pct" id="ac-pct">–</div>
        </div>
        <div class="sv-card-pct-days" id="ac-pct-days">7 ngày</div>
      </div>

      <!-- DOJI -->
      <div class="sv-brand-card" id="sv-card-doji" data-brand="doji">
        <div class="sv-card-head">
          <div class="sv-card-logo" style="background:linear-gradient(135deg,#dc2626,#991b1b)">🔴</div>
          <div class="sv-card-info">
            <div class="sv-card-name">Bạc 99.9 – DOJI</div>
            <div class="sv-card-sub" id="dj-updated">Đang tải...</div>
          </div>
        </div>
        <div class="sv-card-tabs">
          <button class="sv-tab active" data-brand="doji" data-unit="LUONG" data-mult="1">1 Lượng</button>
          <button class="sv-tab" data-brand="doji" data-unit="LUONG" data-mult="5">5 Lượng</button>
        </div>
        <div class="sv-card-prices">
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Mua vào</div>
            <div class="sv-cprice-buy" id="dj-buy">–</div>
          </div>
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Bán ra</div>
            <div class="sv-cprice-sell" id="dj-sell">–</div>
          </div>
          <div class="sv-card-pct" id="dj-pct">–</div>
        </div>
        <div class="sv-card-pct-days" id="dj-pct-days">7 ngày</div>
      </div>


      <!-- Kim Ngân Phúc -->
      <div class="sv-brand-card" id="sv-card-kimnganphuc" data-brand="kimnganphuc">
        <div class="sv-card-head">
          <div class="sv-card-logo" style="background:linear-gradient(135deg,#a78bfa,#7c3aed)">KNP</div>
          <div class="sv-card-info">
            <div class="sv-card-name">Bạc 999  Kim Ngân Phúc</div>
            <div class="sv-card-sub" id="knp-updated">Đang tải...</div>
          </div>
        </div>
        <div class="sv-card-tabs">
          <button class="sv-tab active" data-brand="kimnganphuc" data-unit="KG"    data-mult="1">KG</button>
          <button class="sv-tab"        data-brand="kimnganphuc" data-unit="LUONG" data-mult="1">Lượng</button>
        </div>
        <div class="sv-card-prices">
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Mua vào</div>
            <div class="sv-cprice-buy" id="knp-buy"></div>
          </div>
          <div class="sv-card-price-col">
            <div class="sv-cprice-label">Bán ra</div>
            <div class="sv-cprice-sell" id="knp-sell"></div>
          </div>
          <div class="sv-card-pct" id="knp-pct">–</div>
        </div>
        <div class="sv-card-pct-days" id="knp-pct-days">7 ngày</div>
      </div>

    </div><!-- /sv-brands-grid -->

    <!-- Shared Chart -->
    <div class="sv-shared-chart-wrap">
      <div class="sv-shared-chart-bar">
        <div class="sv-chart-brand-tabs">
          <button class="sv-chart-brand active" data-brand="phuquy">Phú Quý</button>
          <button class="sv-chart-brand" data-brand="ancarat">Ancarat</button>
          <button class="sv-chart-brand" data-brand="doji">DOJI</button>
          <button class="sv-chart-brand" data-brand="kimnganphuc">Kim Ngân Phúc</button>
        </div>
        <div class="sv-chart-period-tabs">
          <button class="sv-prd active" data-days="1">1D</button>
          <button class="sv-prd" data-days="7">7D</button>
          <button class="sv-prd" data-days="30">1M</button>
          <button class="sv-prd" data-days="90">3M</button>
          <button class="sv-prd" data-days="365">1Y</button>
        </div>
        <span class="sv-chart-unit-label" id="sv-chart-unit-lbl">VND/Chỉ</span>
      </div>
      <div class="sv-shared-canvas-wrap">
        <div class="sv-loading" id="sv-chart-loading">
          <div class="sv-spinner"></div> Đang tải biểu đồ...
        </div>
        <canvas id="svSharedChart" style="display:none"></canvas>
      </div>
    </div>

    <p class="sv-footnote">⚠️ Giá tham khảo · Xác nhận từ nguồn chính thức trước khi giao dịch · © 2026 GiáVàng.vn</p>

  </section>

</main>








<script>
/* ══════════════════════════════════════════════════
   SILVER BRANDS: Unified controller
   brands: phuquy | ancarat | doji
══════════════════════════════════════════════════ */
(function () {
  const API = {
    phuquy:  { current: '/api/silver/current',  history: '/api/silver/history',  percent: '/api/silver/percent'  },
    ancarat: { current: '/api/ancarat/current', history: '/api/ancarat/history', percent: '/api/ancarat/percent' },
    doji:    { current: '/api/doji/current',    history: '/api/doji/history',    percent: '/api/doji/percent'    },
    kimnganphuc: { current: '/api/kimnganphuc/current', history: '/api/kimnganphuc/history', percent: '/api/kimnganphuc/percent' },
  };

  // Active state
  let activeBrand = 'phuquy';
  let activePeriod = 1;
  const brandUnit = { phuquy: 'CHI', ancarat: 'LUONG', doji: 'LUONG', kimnganphuc: 'KG' };
  const brandMult = { phuquy: 1, ancarat: 1, doji: 1, kimnganphuc: 1 };

  let sharedChart = null;

  function fmt(n) { return Number(n).toLocaleString('vi-VN'); }

  /* ── Element ID map per brand ── */
  const ELIDS = {
    phuquy:  { buy: 'pq-buy',  sell: 'pq-sell',  updated: 'pq-updated',  pct: 'pq-pct',  pctDays: 'pq-pct-days'  },
    ancarat: { buy: 'ac-buy',  sell: 'ac-sell',  updated: 'ac-updated',  pct: 'ac-pct',  pctDays: 'ac-pct-days'  },
    doji:    { buy: 'dj-buy',  sell: 'dj-sell',  updated: 'dj-updated',  pct: 'dj-pct',  pctDays: 'dj-pct-days'  },
    kimnganphuc: { buy: 'knp-buy', sell: 'knp-sell', updated: 'knp-updated', pct: 'knp-pct', pctDays: 'knp-pct-days' },
  };

  /* ── Load current price for one brand ── */
  function loadBrandPrice(brand) {
    var unit = brandUnit[brand];
    var mult = brandMult[brand];
    var ids  = ELIDS[brand];
    fetch(API[brand].current)
      .then(function(r) { return r.json(); })
      .then(function(json) {
        if (!json.success || !json.data) return;
        var d = json.data[unit];
        if (!d) return;
        document.getElementById(ids.buy).textContent     = fmt(d.buy_price  * mult);
        document.getElementById(ids.sell).textContent    = fmt(d.sell_price * mult);
        document.getElementById(ids.updated).textContent = d.recorded_at || '';
      })
      .catch(function(){});
  }

  /* ── Load % change for one brand ── */
  function loadBrandPct(brand, days) {
    var unit      = brandUnit[brand];
    var ids       = ELIDS[brand];
    fetch(API[brand].percent + '?days=' + days + '&type=' + unit)
      .then(function(r) { return r.json(); })
      .then(function(json) {
        var pctEl     = document.getElementById(ids.pct);
        var pctDaysEl = document.getElementById(ids.pctDays);
        if (!json.success || json.percent === null) { pctEl.textContent = '–'; pctEl.className = 'sv-card-pct'; return; }
        var sign = json.trend === 'up' ? '▲ +' : (json.trend === 'down' ? '▼ -' : '');
        pctEl.textContent = sign + json.percent + '%';
        pctEl.className = 'sv-card-pct ' + (json.trend === 'up' ? 'up' : 'down');
        if (pctDaysEl) pctDaysEl.textContent = days + ' ngày qua';
      }).catch(function(){});
  }

  /* ── Load shared chart ── */
  function loadSharedChart() {
    var brand  = activeBrand;
    var unit   = brandUnit[brand];
    var mult   = brandMult[brand];
    var days   = activePeriod;
    var loading = document.getElementById('sv-chart-loading');
    var canvas  = document.getElementById('svSharedChart');
    loading.style.display = 'flex'; canvas.style.display = 'none';

    fetch(API[brand].history + '?days=' + days + '&type=' + unit)
      .then(function(r) { return r.json(); })
      .then(function(json) {
        loading.style.display = 'none';
        if (!json.success || !json.data || json.data.dates.length === 0) {
          loading.innerHTML = '<span style="color:var(--muted);font-size:12px">Chưa có dữ liệu lịch sử</span>';
          loading.style.display = 'flex'; return;
        }
        canvas.style.display = 'block';

        var lbl = json.type_label || unit;
        if (mult > 1) lbl = 'VND/' + mult + ' Lượng';
        document.getElementById('sv-chart-unit-lbl').textContent = lbl;

        var buys  = json.data.buy_prices.map(function(v)  { return v * mult; });
        var sells = json.data.sell_prices.map(function(v) { return v * mult; });

        if (sharedChart) { sharedChart.destroy(); }
        sharedChart = new Chart(canvas, {
          type: 'line',
          data: {
            labels: json.data.dates,
            datasets: [
              { label: 'Giá bán ra',  data: sells, borderColor: '#4f7af8', backgroundColor: 'rgba(79,122,248,0.06)',  borderWidth: 2, pointRadius: json.data.dates.length <= 15 ? 3 : 1.5, pointHoverRadius: 5, fill: true, tension: 0.38 },
              { label: 'Giá mua vào', data: buys,  borderColor: '#22c97a', backgroundColor: 'rgba(34,201,122,0.08)',  borderWidth: 2, pointRadius: json.data.dates.length <= 15 ? 3 : 1.5, pointHoverRadius: 5, fill: true, tension: 0.38 }
            ]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { labels: { color: '#909ab2', font: { size: 11, family:'Inter' }, usePointStyle:true, pointStyle:'line', pointStyleWidth:20, boxHeight:2 } },
              tooltip: { backgroundColor:'rgba(13,16,24,0.96)', borderColor:'rgba(176,190,197,0.2)', borderWidth:1,
                titleColor:'#e4e8f2', bodyColor:'#909ab2', padding:10,
                callbacks: { label: function(ctx){ return ' ' + ctx.dataset.label + ': ' + Number(ctx.raw).toLocaleString('vi-VN') + ' đ'; } }
              }
            },
            scales: {
              x: { grid:{ color:'rgba(255,255,255,0.04)' }, ticks:{ color:'#6e778c', font:{ size:10 }, maxTicksLimit:10 } },
              y: { grid:{ color:'rgba(255,255,255,0.04)' },
                ticks:{ color:'#6e778c', font:{ size:10 }, callback:function(v){ return Number(v).toLocaleString('vi-VN'); } },
                title:{ display:true, text:lbl, color:'#6e778c', font:{ size:10 } }
              }
            }
          }
        });
      })
      .catch(function() {
        loading.style.display='flex';
        loading.innerHTML='<span style="color:var(--red);font-size:12px">Lỗi tải biểu đồ</span>';
      });
  }

  /* ── Load all current prices ── */
  function loadAllPrices() {
    ['phuquy','ancarat','doji','kimnganphuc'].forEach(function(b) {
      loadBrandPrice(b);
      loadBrandPct(b, activePeriod);
    });
  }

  /* ── Unit tab click inside card ── */
  document.querySelectorAll('.sv-tab').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var brand = btn.dataset.brand;
      var unit  = btn.dataset.unit;
      var mult  = parseInt(btn.dataset.mult) || 1;
      // Deactivate other tabs in same card
      document.querySelectorAll('.sv-tab[data-brand="' + brand + '"]').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      brandUnit[brand] = unit;
      brandMult[brand] = mult;
      loadBrandPrice(brand);
      loadBrandPct(brand, activePeriod);
      if (activeBrand === brand) loadSharedChart();
    });
  });

  /* ── Brand card click → switch chart ── */
  document.querySelectorAll('.sv-brand-card').forEach(function(card) {
    card.addEventListener('click', function() {
      document.querySelectorAll('.sv-brand-card').forEach(function(c){ c.classList.remove('active'); });
      card.classList.add('active');
      activeBrand = card.dataset.brand;
      // Sync chart brand tab
      document.querySelectorAll('.sv-chart-brand').forEach(function(b){ b.classList.remove('active'); });
      var chartTab = document.querySelector('.sv-chart-brand[data-brand="' + activeBrand + '"]');
      if (chartTab) chartTab.classList.add('active');
      loadSharedChart();
    });
  });

  /* ── Chart brand tab click ── */
  document.querySelectorAll('.sv-chart-brand').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.sv-chart-brand').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      activeBrand = btn.dataset.brand;
      // Sync card active
      document.querySelectorAll('.sv-brand-card').forEach(function(c){ c.classList.remove('active'); });
      var card = document.getElementById('sv-card-' + activeBrand);
      if (card) card.classList.add('active');
      loadSharedChart();
    });
  });

  /* ── Period buttons ── */
  document.querySelectorAll('.sv-prd').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.sv-prd').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      activePeriod = parseInt(btn.dataset.days);
      // Reload all pct + chart
      ['phuquy','ancarat','doji','kimnganphuc'].forEach(function(b){ loadBrandPct(b, activePeriod); });
      loadSharedChart();
    });
  });

  /* ── Init ── */
  // Set first card active
  var firstCard = document.getElementById('sv-card-phuquy');
  if (firstCard) firstCard.classList.add('active');

  loadAllPrices();
  loadSharedChart();
  setInterval(function() { loadAllPrices(); loadSharedChart(); }, 30 * 60 * 1000);

})();
</script>

</body>
</html>

