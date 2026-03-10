<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Lịch Sử Giá Bạc Hôm Nay & 30/60/90 Ngày – Biểu Đồ Trực Quan | GiáVàng.vn</title>
  <meta name="description" content="Xem lịch sử giá bạc mua vào bán ra của Phú Quý, Ancarat, DOJI, Kim Ngân Phúc theo ngày và trong ngày. Biểu đồ trực quan, phân tích xu hướng giá."/>
  <link rel="canonical" href="{{ url('/lich-su-gia-bac') }}"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#07090f;--bg2:#0d1018;--bg3:#131724;
      --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.12);
      --gold:#f5c518;--text:#e4e8f2;--text2:#c4cad8;--muted:#6e778c;--muted2:#909ab2;
      --green:#22c97a;--red:#f55252;--blue:#4f7af8;--purple:#a78bfa;
      --radius:14px;--radius-sm:8px;
    }
    html{scroll-behavior:smooth}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    .bg-glow{position:fixed;inset:0;pointer-events:none;z-index:0;
      background:radial-gradient(ellipse 60% 40% at 20% 0%,rgba(167,139,250,0.06) 0%,transparent 60%),
                 radial-gradient(ellipse 50% 35% at 80% 90%,rgba(79,122,248,0.05) 0%,transparent 60%)}

    main{position:relative;z-index:1;max-width:1000px;margin:0 auto;padding:40px 24px 80px}
    @media(max-width:640px){
      main{padding:12px 0 60px}
      .breadcrumb{padding:0 12px}
      .page-head{padding:0 12px}
      .ctrl-bar{padding:0 12px}
      .stats-grid{padding:0 12px}
      .chart-card{border-radius:0;border-left:none;border-right:none}
      .tbl-wrap{border-radius:0;border-left:none;border-right:none}
      .info-card{border-radius:0;border-left:none;border-right:none}
    }
    .breadcrumb{font-size:12px;color:var(--muted);margin-bottom:20px;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .breadcrumb a{color:var(--muted);text-decoration:none}
    .page-head{margin-bottom:32px}
    .page-head-row{display:flex;align-items:center;gap:14px;margin-bottom:8px}
    .page-head-icon{width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#a78bfa,#7c3aed);
      display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 4px 20px rgba(167,139,250,0.3)}
    .page-head h1{font-size:26px;font-weight:800;letter-spacing:-.4px}
    .page-head .sub{font-size:13px;color:var(--muted);margin-top:5px}

    /* CONTROL BAR */
    .ctrl-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:20px}
    .ctrl-group{display:flex;gap:6px;align-items:center}
    .ctrl-label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-right:2px}
    .ctrl-btn{padding:6px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;
      border:1px solid var(--border);background:var(--bg3);color:var(--muted2);cursor:pointer;
      transition:all .18s;font-family:'Inter',sans-serif}
    .ctrl-btn:hover{border-color:var(--border2);color:var(--text)}
    .ctrl-btn.active{background:rgba(167,139,250,0.18);color:var(--purple);border-color:rgba(167,139,250,0.4)}
    .brand-btn{padding:6px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;
      border:1px solid var(--border);background:var(--bg3);color:var(--muted2);cursor:pointer;
      transition:all .18s;font-family:'Inter',sans-serif}
    .brand-btn.active{border-color:var(--purple);background:rgba(167,139,250,0.12);color:var(--text)}


    /* CHART CARD */
    .chart-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);
      overflow:hidden;margin-bottom:28px;box-shadow:0 8px 40px rgba(0,0,0,0.4)}
    .chart-header{padding:16px 20px;border-bottom:1px solid var(--border);
      display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
    .chart-title{font-size:14px;font-weight:700;color:var(--text2)}
    .chart-subtitle{font-size:11px;color:var(--muted);margin-top:2px}
    .chart-unit-lbl{font-size:11px;color:var(--muted);margin-left:auto}
    .chart-body{padding:16px;position:relative;height:320px}
    .chart-body canvas{width:100%!important;height:100%!important}
    .chart-loading{display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;gap:8px}
    .spinner{width:18px;height:18px;border:2px solid var(--border2);border-top-color:var(--purple);border-radius:50%;animation:spin .8s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* STATS ROW */
    .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px}
    @media(max-width:640px){.stats-row{grid-template-columns:repeat(2,1fr)}}
    .stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px}
    .stat-label{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px}
    .stat-val{font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:700;color:var(--text)}
    .stat-sub{font-size:11px;color:var(--muted);margin-top:3px}
    .stat-up{color:var(--green)}.stat-down{color:var(--red)}

    /* DATA TABLE */
    .data-wrap{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:28px}
    .data-wrap-head{padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
    .data-wrap-head h3{font-size:13px;font-weight:700;color:var(--text2)}
    .data-count{font-size:11px;color:var(--muted)}
    .data-tbl{width:100%;border-collapse:collapse;font-size:13px}
    .data-tbl th{padding:10px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
      color:var(--muted);background:var(--bg3);border-bottom:1px solid var(--border);text-align:left}
    .data-tbl td{padding:10px 16px;border-bottom:1px solid var(--border);color:var(--text2)}
    .data-tbl tr:last-child td{border-bottom:none}
    .data-tbl .price{font-family:'JetBrains Mono',monospace;font-weight:600}
    .data-tbl .price-buy{color:var(--red)}.data-tbl .price-sell{color:var(--green)}
    .data-tbl .chg-up{color:var(--green)}.data-tbl .chg-down{color:var(--red)}

    /* INFO */
    .info-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:24px}
    .info-card h2{font-size:16px;font-weight:700;margin-bottom:12px;color:var(--text2)}
    .info-card p{font-size:14px;line-height:1.75;color:var(--muted2);margin-bottom:8px}
  </style>
</head>
<body>
<div class="bg-glow"></div>

@include('frontend.partials.header', ['activePage' => 'lichsu'])

<main>
  <div class="breadcrumb">
    <a href="/">Trang chủ</a><span>›</span><span>Lịch Sử Giá Bạc</span>
  </div>

  <div class="page-head">
    <div class="page-head-row">
      <div class="page-head-icon">📈</div>
      <h1>Lịch Sử Giá Bạc</h1>
    </div>
    <p class="sub">Biểu đồ lịch sử giá bạc mua vào bán ra theo ngày · Chọn thương hiệu và khoảng thời gian</p>
  </div>

  {{-- CONTROL BAR --}}
  <div class="ctrl-bar">
    <span class="ctrl-label">Thương hiệu:</span>
    @foreach($brands as $key => $brand)
    <button class="brand-btn {{ $loop->first ? 'active' : '' }}" data-brand="{{ $key }}" data-api="{{ $brand['api'] }}">{{ $brand['name'] }}</button>
    @endforeach
  </div>
  <div class="ctrl-bar">
    <span class="ctrl-label">Đơn vị:</span>
    <button class="ctrl-btn active" data-unit="KG">KG</button>
    <button class="ctrl-btn" data-unit="LUONG">Lượng</button>
    <span style="margin:0 8px;color:var(--border2)">|</span>
    <span class="ctrl-label">Kỳ:</span>
    <button class="ctrl-btn" data-days="1">1 Ngày</button>
    <button class="ctrl-btn" data-days="7">7 Ngày</button>
    <button class="ctrl-btn active" data-days="30">30 Ngày</button>
    <button class="ctrl-btn" data-days="60">60 Ngày</button>
    <button class="ctrl-btn" data-days="90">90 Ngày</button>
    <button class="ctrl-btn" data-days="365">1 Năm</button>
  </div>

  {{-- STATS --}}
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-label">Giá bán ra hiện tại</div>
      <div class="stat-val" id="stat-sell-now">–</div>
      <div class="stat-sub" id="stat-unit-lbl">VND/đvị</div>
    </div>
    <div class="stat-card">
      <div class="stat-label" id="stat-high-label">Cao nhất kỳ</div>
      <div class="stat-val stat-up" id="stat-high">–</div>
      <div class="stat-sub" id="stat-high-date"></div>
    </div>
    <div class="stat-card">
      <div class="stat-label" id="stat-low-label">Thấp nhất kỳ</div>
      <div class="stat-val stat-down" id="stat-low">–</div>
      <div class="stat-sub" id="stat-low-date"></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Thay đổi kỳ</div>
      <div class="stat-val" id="stat-change">–</div>
      <div class="stat-sub" id="stat-change-pct"></div>
    </div>
  </div>

  {{-- CHART --}}
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <div class="chart-title" id="chart-title">Lịch sử giá bán ra</div>
        <div class="chart-subtitle" id="chart-sub">Đang tải...</div>
      </div>
      <span class="chart-unit-lbl" id="chart-unit-lbl"></span>
    </div>
    <div class="chart-body">
      <div class="chart-loading" id="chart-loading"><div class="spinner"></div> Đang tải biểu đồ...</div>
      <canvas id="histChart" style="display:none"></canvas>
    </div>
  </div>

  {{-- DATA TABLE --}}
  <div class="data-wrap">
    <div class="data-wrap-head">
      <h3>📅 Bảng Dữ Liệu Chi Tiết</h3>
      <span class="data-count" id="data-count"></span>
    </div>
    <div style="overflow-x:auto;max-height:360px;overflow-y:auto">
      <table class="data-tbl" id="data-tbl">
        <thead>
          <tr>
            <th>Ngày</th>
            <th>Mua vào</th>
            <th>Bán ra</th>
            <th>Chênh lệch</th>
            <th>Thay đổi bán ra</th>
          </tr>
        </thead>
        <tbody id="data-tbody"><tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px">Đang tải...</td></tr></tbody>
      </table>
    </div>
  </div>

  {{-- SEO INFO --}}
  <div class="info-card">
    <h2>📖 Về Dữ Liệu Lịch Sử Giá Bạc</h2>
    <p>Dữ liệu lịch sử giá bạc được thu thập tự động từ các thương hiệu uy tín: Phú Quý, Ancarat, DOJI và Kim Ngân Phúc. Mỗi điểm dữ liệu đại diện cho giá chốt cuối ngày hoặc theo giờ (kỳ 1 ngày).</p>
    <p>Nhà đầu tư có thể sử dụng biểu đồ lịch sử để phân tích xu hướng giá trong ngày hoặc theo kỳ dài hạn, từ đó đưa ra quyết định đầu tư phù hợp.</p>
  </div>
</main>

@include('frontend.partials.footer')

<script>
(function() {
  var activeBrand = 'phuquy';
  var activeApi   = 'silver';
  var activeUnit  = 'KG';
  var activeDays  = 30;
  var histChart   = null;
  var chartData   = null; // cached data for CSV

  var fmt = function(n) { return Number(n).toLocaleString('vi-VN'); };

  function getApiUrl() {
    return '/api/' + activeApi + '/history?days=' + activeDays + '&type=' + activeUnit;
  }

  function loadChart() {
    var loading = document.getElementById('chart-loading');
    var canvas  = document.getElementById('histChart');
    loading.style.display = 'flex';
    canvas.style.display  = 'none';
    chartData = null;

    fetch(getApiUrl())
      .then(function(r){if(!r.ok)throw new Error(r.status);return r.json();})
      .then(function(json) {
        loading.style.display = 'none';
        if (!json.success || !json.data || json.data.dates.length === 0) {
          loading.innerHTML = '<span style="color:var(--muted);font-size:12px">⚠️ Chưa có dữ liệu lịch sử cho kỳ này</span>';
          loading.style.display = 'flex';
          return;
        }
        chartData = json;
        canvas.style.display = 'block';

        var dates  = json.data.dates;
        var buys   = json.data.buy_prices;
        var sells  = json.data.sell_prices;

        // Stats
        var maxSell = Math.max.apply(null, sells);
        var minSell = Math.min.apply(null, sells);
        var maxIdx  = sells.indexOf(maxSell);
        var minIdx  = sells.indexOf(minSell);
        var firstSell = sells[0], lastSell = sells[sells.length - 1];
        var chg = lastSell - firstSell;
        var pct = firstSell > 0 ? (chg / firstSell * 100).toFixed(2) : 0;

        var isIntraday = (activeDays === 1);
        document.getElementById('stat-sell-now').textContent = fmt(lastSell);
        document.getElementById('stat-unit-lbl').textContent = json.type_label || activeUnit;
        document.getElementById('stat-high').textContent = fmt(maxSell);
        document.getElementById('stat-high-date').textContent = isIntraday ? ('lúc ' + (dates[maxIdx] || '')) : (dates[maxIdx] || '');
        document.getElementById('stat-high-label').textContent = isIntraday ? 'Cao nhất hôm nay' : 'Cao nhất kỳ';
        document.getElementById('stat-low').textContent  = fmt(minSell);
        document.getElementById('stat-low-date').textContent  = isIntraday ? ('lúc ' + (dates[minIdx] || '')) : (dates[minIdx] || '');
        document.getElementById('stat-low-label').textContent = isIntraday ? 'Thấp nhất hôm nay' : 'Thấp nhất kỳ';
        document.getElementById('stat-change').textContent = (chg >= 0 ? '+' : '') + fmt(chg);
        document.getElementById('stat-change').className = 'stat-val ' + (chg >= 0 ? 'stat-up' : 'stat-down');
        document.getElementById('stat-change-pct').textContent = (chg >= 0 ? '▲ +' : '▼ ') + pct + '%' + (isIntraday ? ' trong ngày' : ' kỳ ' + activeDays + ' ngày');
        document.getElementById('chart-sub').textContent = isIntraday
          ? ('Hôm nay · ' + dates[0] + ' → ' + dates[dates.length-1])
          : (activeDays + ' ngày · ' + dates[0] + ' → ' + dates[dates.length-1]);

        // Crosshair plugin
        var crosshair = {
          id:'hCrosshair',
          afterDraw:function(c){
            if(c._chX==null)return;
            var ctx=c.ctx,y=c.scales.y,x=c.scales.x;
            ctx.save();ctx.setLineDash([4,4]);ctx.lineWidth=1;
            ctx.strokeStyle='rgba(255,255,255,0.18)';
            ctx.beginPath();ctx.moveTo(c._chX,y.top);ctx.lineTo(c._chX,y.bottom);ctx.stroke();
            ctx.beginPath();ctx.moveTo(x.left,c._chY);ctx.lineTo(x.right,c._chY);ctx.stroke();
            ctx.restore();
          }
        };

        if (histChart) histChart.destroy();
        histChart = new Chart(canvas, {
          type: 'line',
          data: {
            labels: dates,
            datasets: [
              {label:'Giá bán ra',  data:sells, borderColor:'#4f7af8', backgroundColor:'rgba(79,122,248,0.07)', borderWidth:2, pointRadius:dates.length<=20?3:1.5, pointHoverRadius:5, fill:true, tension:0.35},
              {label:'Giá mua vào', data:buys,  borderColor:'#22c97a', backgroundColor:'rgba(34,201,122,0.06)', borderWidth:2, pointRadius:dates.length<=20?3:1.5, pointHoverRadius:5, fill:true, tension:0.35}
            ]
          },
          plugins: [crosshair],
          options: {
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            onHover:function(e,_,c){if(e.native){c._chX=e.x;c._chY=e.y;}},
            plugins:{
              legend:{labels:{color:'#909ab2',font:{size:11,family:'Inter'},usePointStyle:true,pointStyle:'line',pointStyleWidth:20,boxHeight:2}},
              tooltip:{backgroundColor:'rgba(13,16,24,0.97)',borderColor:'rgba(255,255,255,0.1)',borderWidth:1,
                titleColor:'#e4e8f2',bodyColor:'#909ab2',padding:10,
                callbacks:{label:function(ctx){return ' '+ctx.dataset.label+': '+Number(ctx.raw).toLocaleString('vi-VN')+' đ';}}}
            },
            scales:{
              x:{grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#6e778c',font:{size:10},maxTicksLimit:12}},
              y:{grid:{color:'rgba(255,255,255,0.04)'},
                ticks:{color:'#6e778c',font:{size:10},callback:function(v){return Number(v).toLocaleString('vi-VN');}},
                title:{display:true,text:json.type_label||activeUnit,color:'#6e778c',font:{size:10}}
              }
            }
          }
        });

        canvas.onmouseleave = function(){if(histChart){histChart._chX=null;histChart._chY=null;histChart.draw();}};

        // Populate data table (last 50 rows, reversed — newest first)
        buildTable(dates, buys, sells);
      })
      .catch(function(err){
        document.getElementById('chart-loading').innerHTML='<span style="color:var(--muted);font-size:12px">⚠️ Lỗi tải dữ liệu</span>';
        document.getElementById('chart-loading').style.display='flex';
        console.warn('HistChart error:', err);
      });
  }

  function buildTable(dates, buys, sells) {
    var tbody = document.getElementById('data-tbody');
    var rows  = [];
    for (var i = 0; i < dates.length; i++) {
      var chg   = (i > 0) ? sells[i] - sells[i-1] : null;
      rows.push({date: dates[i], buy: buys[i], sell: sells[i], chg: chg});
    }
    rows.reverse(); // newest first
    var limit = Math.min(rows.length, 60);
    document.getElementById('data-count').textContent = rows.length + (activeDays === 1 ? ' điểm dữ liệu' : ' ngày dữ liệu');
    var html = '';
    for (var j = 0; j < limit; j++) {
      var r   = rows[j];
      var chgCls = r.chg === null ? '' : (r.chg > 0 ? 'chg-up' : (r.chg < 0 ? 'chg-down' : ''));
      var chgStr = r.chg === null ? '–' : ((r.chg > 0 ? '▲ +' : (r.chg < 0 ? '▼ ' : '')) + fmt(Math.abs(r.chg)));
      html += '<tr><td>'+ r.date +'</td>'
            + '<td class="price price-buy">'+ fmt(r.buy)  +'</td>'
            + '<td class="price price-sell">'+ fmt(r.sell) +'</td>'
            + '<td class="price">'+ fmt(r.sell - r.buy)  +'</td>'
            + '<td class="'+ chgCls +'">'+ chgStr +'</td></tr>';
    }
    tbody.innerHTML = html;
  }


  // Brand buttons
  document.querySelectorAll('.brand-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.brand-btn').forEach(function(b){b.classList.remove('active')});
      btn.classList.add('active');
      activeBrand = btn.dataset.brand;
      activeApi   = btn.dataset.api;
      // Reset unit to KG for brands that have it, LUONG for DOJI
      if (activeBrand === 'doji') {
        activeUnit = 'LUONG';
        document.querySelectorAll('.ctrl-btn[data-unit]').forEach(function(b){
          b.classList.toggle('active', b.dataset.unit === 'LUONG');
        });
      }
      document.getElementById('chart-title').textContent = 'Lịch sử giá bán ra — ' + btn.textContent.trim();
      loadChart();
    });
  });

  // Unit buttons
  document.querySelectorAll('.ctrl-btn[data-unit]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.ctrl-btn[data-unit]').forEach(function(b){b.classList.remove('active')});
      btn.classList.add('active');
      activeUnit = btn.dataset.unit;
      loadChart();
    });
  });

  // Period buttons
  document.querySelectorAll('.ctrl-btn[data-days]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.ctrl-btn[data-days]').forEach(function(b){b.classList.remove('active')});
      btn.classList.add('active');
      activeDays = parseInt(btn.dataset.days);
      loadChart();
    });
  });

  // Init
  loadChart();
})();
</script>
</body>
</html>
