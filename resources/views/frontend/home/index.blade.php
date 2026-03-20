@extends('frontend.partials.layout', ['activePage' => 'home', 'maxWidth' => '1600px'])

@section('title', 'Giá Vàng & Bạc Hôm Nay – Cập Nhật Real-time | GiáVàng.vn')

@section('meta')
  <meta name="description" content="Giá vàng và bạc hôm nay cập nhật real-time. Biểu đồ giá vàng thế giới XAU/USD, giá bạc 999 Phú Quý, Ancarat, DOJI, Kim Ngân Phúc. So sánh & quy đổi giá bạc trực tuyến."/>
  <link rel="canonical" href="{{ url('/') }}"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="Giá Vàng & Bạc Hôm Nay – Cập Nhật Real-time | GiáVàng.vn"/>
  <meta property="og:description" content="Giá vàng và bạc hôm nay cập nhật real-time. Biểu đồ giá vàng thế giới, giá bạc 999 các thương hiệu uy tín tại Việt Nam."/>
  <meta property="og:url" content="{{ url('/') }}"/>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "GiáVàng.vn",
    "url": "{{ url('/') }}",
    "description": "Giá vàng và bạc hôm nay cập nhật real-time. Biểu đồ giá vàng thế giới, giá bạc 999 các thương hiệu uy tín.",
    "publisher": {
      "@type": "Organization",
      "name": "GiáVàng.vn",
      "url": "{{ url('/') }}"
    }
  }
  </script>
@endsection

@section('bg-glow')
<div class="bg-glow" style="position:fixed;inset:0;pointer-events:none;z-index:0;
  background:radial-gradient(ellipse 70% 45% at 15% 0%,rgba(79,122,248,0.08) 0%,transparent 60%),
             radial-gradient(ellipse 60% 40% at 85% 90%,rgba(245,197,24,0.05) 0%,transparent 60%)"></div>
@endsection

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="/frontend/css/home.css"/>
@endpush

@section('content')
  <!-- Page title -->
  <div class="page-header">
    <h1>📊 Giá Vàng & Bạc – Tổng Hợp</h1>
    <p>Biểu đồ realtime thế giới · Giá bạc Phú Quý cập nhật real-time</p>
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
            {"proName": "OANDA:XAUUSD",    "title": "Vàng – Gold"},
            {"proName": "OANDA:XAGUSD",    "title": "Bạc – Silver"},
            {"proName": "FX_IDC:USDVND",   "title": "USD/VND"},
            {"proName": "BITSTAMP:BTCUSD",  "title": "Bitcoin – BTC"},
            {"proName": "BITSTAMP:ETHUSD",  "title": "Ethereum – ETH"},
            {"proName": "BINANCE:SOLUSDT",  "title": "Solana – SOL"},
            {"proName": "BINANCE:WLDUSDT",  "title": "Worldcoin – WLD"}
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

  <!-- ══ SECTION: Giá Vàng & Bạc Thế Giới ══ -->
  <section class="world-charts-section" id="section-world">
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
        <p>Phú Quý · Ancarat · DOJI · Cập nhật real-time</p>
      </div>
    </div>

    <div class="sv-main-layout">
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
          <button class="sv-tab" data-brand="phuquy" data-unit="LUONG" data-mult="1">Lượng</button>
          <button class="sv-tab active" data-brand="phuquy" data-unit="KG" data-mult="1">KG</button>
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
        <div class="sv-card-bottom-row">
          <div class="sv-card-spread">Chênh lệch: <span class="spread-val" id="pq-spread">–</span></div>
          <div class="sv-card-pct-days" id="pq-pct-days">7 ngày qua</div>
        </div>
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
          <button class="sv-tab" data-brand="ancarat" data-unit="LUONG" data-mult="1">Lượng</button>
          <button class="sv-tab active" data-brand="ancarat" data-unit="KG" data-mult="1">KG</button>
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
        <div class="sv-card-bottom-row">
          <div class="sv-card-spread">Chênh lệch: <span class="spread-val" id="ac-spread">–</span></div>
          <div class="sv-card-pct-days" id="ac-pct-days">7 ngày qua</div>
        </div>
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
        <div class="sv-card-bottom-row">
          <div class="sv-card-spread">Chênh lệch: <span class="spread-val" id="dj-spread">–</span></div>
          <div class="sv-card-pct-days" id="dj-pct-days">7 ngày qua</div>
        </div>
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
          <button class="sv-tab" data-brand="kimnganphuc" data-unit="LUONG" data-mult="1">Lượng</button>
          <button class="sv-tab active" data-brand="kimnganphuc" data-unit="KG"    data-mult="1">KG</button>
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
        <div class="sv-card-bottom-row">
          <div class="sv-card-spread">Chênh lệch: <span class="spread-val" id="knp-spread">–</span></div>
          <div class="sv-card-pct-days" id="knp-pct-days">7 ngày qua</div>
        </div>
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
          <button class="sv-prd" data-days="1">1D</button>
          <button class="sv-prd active" data-days="7">7D</button>
          <button class="sv-prd" data-days="30">1M</button>
          <button class="sv-prd" data-days="90">3M</button>
          <button class="sv-prd" data-days="365">1Y</button>
        </div>
        <div class="sv-chart-unit-tabs" id="sv-chart-unit-tabs"></div>
        <span class="sv-chart-unit-label" id="sv-chart-unit-lbl">VND/KG</span>
      </div>
      <div class="sv-shared-canvas-wrap">
        <div class="sv-loading" id="sv-chart-loading">
          <div class="sv-spinner"></div> Đang tải biểu đồ...
        </div>
        <canvas id="svSharedChart" style="display:none"></canvas>
      </div>
    </div>

    </div><!-- /sv-main-layout -->
  </section>
@endsection

@push('scripts')
<script src="/frontend/js/home.js"></script>
@endpush
