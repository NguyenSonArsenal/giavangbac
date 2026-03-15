{{-- ═══════════════════════════════════════════════════════
     SHARED HEADER / NAV — @include('frontend.partials.header')
     Pass $activePage = 'home'|'phuquy'|'ancarat'|'doji'|'knp'|'quydoi'|'sosanh'|'lichsu'
═══════════════════════════════════════════════════════ --}}

<style>
/* ── HEADER ── */
header{position:sticky;top:0;z-index:100;background:rgba(7,9,15,0.92);backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;gap:16px}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.logo-icon{width:34px;height:34px;background:linear-gradient(135deg,var(--gold,#f5c518),#c8820a);border-radius:50%;
  display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;
  color:#07090f;box-shadow:0 0 18px rgba(245,197,24,0.4)}
.logo-text{font-size:20px;font-weight:800;background:linear-gradient(90deg,#ffd76e,#f5c518);
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.logo-text span{-webkit-text-fill-color:var(--muted,#6e778c);font-weight:400;font-size:13px}
.header-tag{font-size:12px;color:var(--muted,#6e778c);display:flex;align-items:center;gap:6px}

/* Nav */
.header-nav{display:flex;align-items:center;gap:2px;margin-left:auto;flex:1;justify-content:flex-end}
.nav-link{font-size:12.5px;font-weight:500;color:var(--muted2,#909ab2);text-decoration:none;
  padding:6px 10px;border-radius:7px;transition:color .18s,background .18s;white-space:nowrap}
.nav-link:hover{color:var(--text,#e4e8f2);background:rgba(255,255,255,0.05)}
.nav-link.active{color:#ffd76e}

/* Dropdown */
.nav-dropdown{position:relative}
.nav-dropdown-toggle{font-size:13px;font-weight:500;color:var(--muted2,#909ab2);
  padding:6px 12px;border-radius:7px;cursor:pointer;display:flex;align-items:center;gap:5px;
  transition:color .18s,background .18s;user-select:none}
.nav-dropdown-toggle:hover{color:var(--text,#e4e8f2);background:rgba(255,255,255,0.05)}
.nav-caret{font-size:10px;transition:transform .2s}
.nav-dropdown.open .nav-caret{transform:rotate(180deg)}
.nav-dropdown-menu{position:absolute;top:calc(100% + 8px);left:0;background:#0d1018;
  border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:6px;min-width:200px;z-index:200;
  box-shadow:0 12px 40px rgba(0,0,0,0.6);display:none}
.nav-dropdown.open .nav-dropdown-menu{display:block}
.nav-dropdown-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:6px;
  text-decoration:none;color:var(--text2,#c4cad8);font-size:13px;font-weight:500;transition:background .15s}
.nav-dropdown-item:hover{background:rgba(255,255,255,0.06);color:var(--text,#e4e8f2)}
.nav-dropdown-icon{width:26px;height:26px;border-radius:6px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:13px}
.nav-dropdown-sub{font-size:10.5px;color:var(--muted,#6e778c);margin-top:1px}

/* Mobile hamburger */
.nav-toggle{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;margin-left:auto;
  background:none;border:none}
.nav-toggle span{display:block;width:22px;height:2px;background:var(--muted2,#909ab2);border-radius:2px;transition:all .25s}
@media(max-width:900px){
  .header-nav{display:none}.nav-toggle{display:flex}.header-tag{display:none}
  header{padding:0 16px}
}

/* Mobile drawer */
.nav-drawer{position:fixed;inset:0;z-index:300;pointer-events:none}
.nav-drawer-overlay{position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .3s}
.nav-drawer-panel{position:absolute;top:0;right:0;bottom:0;width:260px;background:#0d1018;
  border-left:1px solid rgba(255,255,255,0.12);padding:20px 16px;
  transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);overflow-y:auto}
.nav-drawer.open{pointer-events:all}
.nav-drawer.open .nav-drawer-overlay{background:rgba(0,0,0,0.55)}
.nav-drawer.open .nav-drawer-panel{transform:translateX(0)}
.drawer-close{display:flex;justify-content:flex-end;margin-bottom:20px}
.drawer-close button{background:none;border:none;color:var(--muted2,#909ab2);font-size:22px;cursor:pointer;line-height:1}
.drawer-link{display:flex;align-items:center;gap:10px;padding:11px 10px;border-radius:8px;
  text-decoration:none;color:var(--text2,#c4cad8);font-size:14px;font-weight:500;
  transition:background .15s;margin-bottom:2px}
.drawer-link:hover{background:rgba(255,255,255,0.05);color:var(--text,#e4e8f2)}
.drawer-divider{font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted,#6e778c);padding:12px 10px 6px}
.drawer-icon{width:28px;height:28px;border-radius:7px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px}
</style>

<header>
  <a href="/" class="logo">
    <div class="logo-icon">G</div>
    <div class="logo-text">GiáVàng<span>.vn</span></div>
  </a>

  {{-- Desktop Nav --}}
  <nav class="header-nav">
    <a href="/" class="nav-link {{ ($activePage ?? '') === 'home' ? 'active' : '' }}">Trang Chủ</a>

    <div class="nav-dropdown" id="nav-bac">
      <div class="nav-dropdown-toggle">
        🥈 Giá Bạc <span class="nav-caret">▾</span>
      </div>
      <div class="nav-dropdown-menu">
        <a href="/gia-bac-phu-quy" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#b0bec5,#546e7a)">🥈</div>
          <div><div>Phú Quý 999</div><div class="nav-dropdown-sub">giá bạc phú quý hôm nay</div></div>
        </a>
        <a href="/gia-bac-ancarat" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#06b6d4,#0284c7)">🏅</div>
          <div><div>Ancarat 999</div><div class="nav-dropdown-sub">giá bạc ancarat hôm nay</div></div>
        </a>
        <a href="/gia-bac-doji" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#dc2626,#991b1b)">🔴</div>
          <div><div>DOJI 99.9</div><div class="nav-dropdown-sub">giá bạc doji hôm nay</div></div>
        </a>
        <a href="/gia-bac-kim-ngan-phuc" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);font-size:10px;font-weight:900;color:#fff">KNP</div>
          <div><div>Kim Ngân Phúc 999</div><div class="nav-dropdown-sub">giá bạc kim ngân phúc</div></div>
        </a>
      </div>
    </div>

    <a href="/#section-world" class="nav-link">🌍 Giá Thế Giới</a>
    <a href="/quy-doi-bac" class="nav-link {{ ($activePage ?? '') === 'quydoi' ? 'active' : '' }}">⚖️ Quy Đổi</a>
    <a href="/so-sanh-gia-bac" class="nav-link {{ ($activePage ?? '') === 'sosanh' ? 'active' : '' }}">📊 So Sánh</a>
    <a href="/lich-su-gia-bac" class="nav-link {{ ($activePage ?? '') === 'lichsu' ? 'active' : '' }}">📈 Lịch Sử</a>

    <div class="nav-dropdown" id="nav-baiviet">
      <div class="nav-dropdown-toggle">
        📝 Bài Viết <span class="nav-caret">▾</span>
      </div>
      <div class="nav-dropdown-menu">
        <a href="/bai-viet" class="nav-dropdown-item" style="border-bottom:1px solid rgba(255,255,255,0.08);margin-bottom:4px;padding-bottom:12px">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#4f7af8,#2563eb)">📝</div>
          <div><div style="font-weight:700">Tất Cả Bài Viết</div><div class="nav-dropdown-sub">kiến thức vàng bạc & đầu tư</div></div>
        </a>
        <a href="/bac-999-la-gi" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#f5c518,#c8820a)">📖</div>
          <div><div>Bạc 999 Là Gì?</div><div class="nav-dropdown-sub">tìm hiểu bạc nguyên chất</div></div>
        </a>
        <a href="/nen-mua-bac-o-dau" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#22c97a,#059669)">🛒</div>
          <div><div>Mua Bạc Ở Đâu?</div><div class="nav-dropdown-sub">so sánh thương hiệu uy tín</div></div>
        </a>
        <a href="/bac-co-phai-kenh-dau-tu-tot" class="nav-dropdown-item">
          <div class="nav-dropdown-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed)">📈</div>
          <div><div>Đầu Tư Bạc</div><div class="nav-dropdown-sub">phân tích lợi nhuận & rủi ro</div></div>
        </a>
      </div>
    </div>
  </nav>

  <div class="header-tag" style="margin-left:auto">
    <span class="live-dot"></span> Dữ liệu trực tiếp
  </div>

  {{-- Mobile hamburger --}}
  <button class="nav-toggle" id="nav-toggle" aria-label="Mở menu">
    <span></span><span></span><span></span>
  </button>
</header>

{{-- Mobile Drawer --}}
<div class="nav-drawer" id="nav-drawer">
  <div class="nav-drawer-overlay" id="nav-overlay"></div>
  <div class="nav-drawer-panel">
    <div class="drawer-close"><button id="nav-close" aria-label="Đóng">×</button></div>
    <a href="/" class="drawer-link"><span>🏠</span> Trang Chủ</a>
    <div class="drawer-divider">Giá Bạc Thương Hiệu</div>
    <a href="/gia-bac-phu-quy" class="drawer-link">
      <div class="drawer-icon" style="background:linear-gradient(135deg,#b0bec5,#546e7a)">🥈</div> Phú Quý 999
    </a>
    <a href="/gia-bac-ancarat" class="drawer-link">
      <div class="drawer-icon" style="background:linear-gradient(135deg,#06b6d4,#0284c7)">🏅</div> Ancarat 999
    </a>
    <a href="/gia-bac-doji" class="drawer-link">
      <div class="drawer-icon" style="background:linear-gradient(135deg,#dc2626,#991b1b)">🔴</div> DOJI 99.9
    </a>
    <a href="/gia-bac-kim-ngan-phuc" class="drawer-link">
      <div class="drawer-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);font-size:10px;font-weight:900;color:#fff">KNP</div> Kim Ngân Phúc 999
    </a>
    <div class="drawer-divider">Biểu Đồ</div>
    <a href="/#section-world" class="drawer-link" id="drawer-world"><span>🌍</span> Giá Thế Giới</a>
    <div class="drawer-divider">Công Cụ</div>
    <a href="/quy-doi-bac" class="drawer-link"><span>⚖️</span> Quy Đổi Giá Bạc</a>
    <a href="/so-sanh-gia-bac" class="drawer-link"><span>📊</span> So Sánh Giá Bạc</a>
    <a href="/lich-su-gia-bac" class="drawer-link"><span>📈</span> Lịch Sử Giá Bạc</a>
    <div class="drawer-divider">Bài Viết</div>
    <a href="/bai-viet" class="drawer-link" style="font-weight:700;color:var(--blue,#4f7af8)"><span>📝</span> Tất Cả Bài Viết</a>
    <a href="/bac-999-la-gi" class="drawer-link"><span>📖</span> Bạc 999 Là Gì?</a>
    <a href="/nen-mua-bac-o-dau" class="drawer-link"><span>🛒</span> Mua Bạc Ở Đâu?</a>
    <a href="/bac-co-phai-kenh-dau-tu-tot" class="drawer-link"><span>📈</span> Đầu Tư Bạc</a>
  </div>
</div>

<script>
/* ── NAV: dropdown + mobile drawer ── */
(function(){
  var dropBtn=document.querySelector('#nav-bac .nav-dropdown-toggle');
  var dropEl=document.getElementById('nav-bac');
  if(dropBtn&&dropEl){
    dropBtn.addEventListener('click',function(e){e.stopPropagation();dropEl.classList.toggle('open');});
    document.addEventListener('click',function(){dropEl.classList.remove('open');});
  }
  var drawer=document.getElementById('nav-drawer');
  var toggle=document.getElementById('nav-toggle');
  var closeBtn=document.getElementById('nav-close');
  var overlay=document.getElementById('nav-overlay');
  function openDrawer(){if(drawer)drawer.classList.add('open');}
  function closeDrawer(){if(drawer)drawer.classList.remove('open');}
  if(toggle)toggle.addEventListener('click',openDrawer);
  if(closeBtn)closeBtn.addEventListener('click',closeDrawer);
  if(overlay)overlay.addEventListener('click',closeDrawer);
  var drawerWorld=document.getElementById('drawer-world');
  if(drawerWorld)drawerWorld.addEventListener('click',closeDrawer);
  // Dropdown – Bài Viết
  var dropBv=document.querySelector('#nav-baiviet .nav-dropdown-toggle');
  var dropBvEl=document.getElementById('nav-baiviet');
  if(dropBv&&dropBvEl){
    dropBv.addEventListener('click',function(e){e.stopPropagation();dropBvEl.classList.toggle('open');if(dropEl)dropEl.classList.remove('open');});
    document.addEventListener('click',function(){dropBvEl.classList.remove('open');});
  }
  // Update ESC handler
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){closeDrawer();if(dropEl)dropEl.classList.remove('open');if(dropBvEl)dropBvEl.classList.remove('open');}
  });
})();
</script>
