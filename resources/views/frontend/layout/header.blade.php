<header class="header">
  <div class="container">
    <div class="header_wrap">
      <!-- LEFT -->
      <a class="header_logo" href="{{ clientRoute('home') }}">
        <img src="{{ asset('frontend/image/logo.jpg') }}" alt="Longevity Pharma">
      </a>

      <!-- CENTER (desktop) -->
      <div class="header_center">
        <div class="header_lang">
          <button class="header_lang_btn" type="button">
            <img class="header_flag" src="{{ asset('frontend/image/flags/vi.svg') }}" alt="VN">
            <span class="header_lang_text">Tiếng Việt</span>
            <span class="header_caret">▾</span>
          </button>

          <div class="header_lang_menu">
            <button class="header_lang_item is_active" type="button" data-lang="vi">
              <img src="{{ asset('frontend/image/flags/vi.svg') }}" alt="VN">
              <span>Tiếng Việt</span>
            </button>

            <button class="header_lang_item" type="button" data-lang="en">
              <img src="{{ asset('frontend/image/flags/en.svg') }}" alt="EN">
              <span>English</span>
            </button>
          </div>
        </div>

        <span class="header_divider"></span>

        <nav class="header_nav">
          <a class="header_link {{ request()->routeIs('*home*') ? 'is_active' : '' }}" href="{{ clientRoute('home') }}">Trang Chủ</a>

          <div class="header_dropdown">
            <a class="header_link {{ request()->routeIs('*about*') ? 'is_active' : '' }}" href="{{ clientRoute('about') }}">Về chúng tôi</a>
          </div>

          <div class="header_dropdown">
            <a class="header_link {{ request()->routeIs('*san_pham*') ? 'is_active' : '' }}" href="{{ clientRoute('san_pham') }}">Sản phẩm</a>
          </div>

          <div class="header_dropdown">
            <a class="header_link" href="/san-pham">Danh mục <span class="header_caret">▾</span></a>

            <div class="header_menu">
              @foreach($menuCategories as $p)
                @php
                  $pActive = request()->is($p->slug) || request()->is($p->slug.'/*');
                @endphp

                <div class="menu_item {{ $p->children->count() ? 'has_sub' : '' }}">
                  <a href="/{{ $p->slug }}" class="{{ $pActive ? 'is_active' : '' }}">
                    @if($p->children->count())
                      <span class="menu_count">{{ $p->name }} ({{ $p->children->count() }})</span>
                    @else
                      {{ $p->name }}
                    @endif
                  </a>

                  @if($p->children->count())
                    <div class="submenu">
                      @foreach($p->children as $c)
                        @php
                          $cActive = request()->is($c->slug) || request()->is($c->slug.'/*');
                        @endphp
                        <a href="/{{ $c->slug }}" class="{{ $cActive ? 'is_active' : '' }}">
                          {{ $c->name }}
                        </a>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach

            </div>
          </div>

          <a class="header_link {{ request()->routeIs('*tin_tuc*') ? 'is_active' : '' }}" href="{{ clientRoute('tin_tuc') }}">Tin tức</a>

          <div class="header_dropdown">
            <a class="header_link {{ request()->routeIs('chinh_sach_dai_ly') || request()->routeIs('*dai_ly*') ? 'is_active' : '' }}" href="javascript:void(0);">Đại lý <span class="header_caret">▾</span></a>
            <div class="header_menu">
              <a href="{{ clientRoute('chinh_sach_dai_ly') }}">Chính sách</a>
              <a href="{{ clientRoute('dai_ly') }}">Đăng ký</a>
            </div>
          </div>

          <a class="header_link {{ request()->routeIs('*contact*') ? 'is_active' : '' }}" href="{{ clientRoute('contact.index') }}">Liên hệ</a>

          @if(clientGuard()->check())
            <div class="header_dropdown">
              <a class="header_link" href="javascript:void(0);">Hi, <strong>{{ clientGuard()->user()->username }}</strong> <span class="header_caret">▾</span></a>
              <div class="header_menu">
                <a href="{{ clientRoute('account.index') }}">Account</a>
                <a href="{{ clientRoute('auth.logout') }}">Logout</a>
              </div>
            </div>
          @endif
        </nav>
      </div>

      <!-- RIGHT -->
      <div class="header_right">
        @if(!clientGuard()->check())
          <a class="header_icon" href="{{ clientRoute('auth.login') }}" aria-label="Tài khoản"
             style="display:inline-flex; align-items:center; justify-content:center;">
            <i class="fa-regular fa-user"></i>
          </a>
        @endif

        <a class="header_icon header_cart" href="{{ clientRoute('cart') }}" aria-label="Giỏ hàng"
           style="display:inline-flex; align-items:center; justify-content:center; position:relative;">
          <i class="fa fa-shopping-cart" aria-hidden="true"></i>
          <span class="header_badge" id="cart-badge">{{ count(session('cart', [])) }}</span>
        </a>

        <!-- Hamburger (mobile) -->
        <button class="header_toggle" type="button" aria-label="Mở menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </div>

  <!-- Overlay + Drawer -->
  <div class="header_overlay"></div>

  <aside class="header_drawer" aria-hidden="true">
    <div class="drawer_head">
      <div class="drawer_title">Menu</div>
      <button class="drawer_close" type="button" aria-label="Đóng menu">×</button>
    </div>

    <div class="drawer_body">
      <div class="drawer_lang">
        <button class="drawer_lang_btn" type="button">
          <img class="header_flag" src="{{ asset('frontend/image/flags/vi.svg') }}" alt="VN">
          <span class="drawer_lang_text">Tiếng Việt</span>
          <span class="drawer_caret">▾</span>
        </button>

        <div class="drawer_lang_menu">
          <button class="drawer_lang_item is_active" type="button" data-lang="vi"
                  data-flag="{{ asset('frontend/image/flags/vi.svg') }}">
            <img class="header_flag" src="{{ asset('frontend/image/flags/vi.svg') }}" alt="VN">
            <span>Tiếng Việt</span>
          </button>

          <button class="drawer_lang_item" type="button" data-lang="en" data-flag=en">
            <img class="header_flag" src="{{ asset('frontend/image/flags/en.svg') }}" alt="EN">
            <span>English</span>
          </button>
        </div>
      </div>

      <nav class="drawer_nav">
        <a href="{{ clientRoute('home') }}" class="drawer_link {{ request()->routeIs('*home*') ? 'is_active' : '' }}">Trang Chủ</a>
        <a href="{{ clientRoute('about') }}" class="drawer_link {{ request()->routeIs('*about*') ? 'is_active' : '' }}">Về chúng tôi</a>
        <a href="{{ clientRoute('san_pham') }}" class="drawer_link {{ request()->routeIs('*san_pham*') ? 'is_active' : '' }}">Sản phẩm</a>

        <div class="drawer_group">
          <button class="drawer_parent" type="button">
            Danh mục<span class="drawer_caret">▾</span>
          </button>
          <div class="drawer_sub">
            @foreach($menuCategories as $p)
              @if($p->children->count())
                <div class="drawer_group">
                  <button class="drawer_parent" type="button">
                    {{ $p->name }} <span class="drawer_caret">▾</span>
                  </button>
                  <div class="drawer_sub">
                    @foreach($p->children as $c)
                      <a href="/{{ $c->slug }}" class="drawer_link {{ request()->is($c->slug) || request()->is($c->slug.'/*') ? 'is_active' : '' }}">{{ $c->name }}</a>
                    @endforeach
                  </div>
                </div>
              @else
                <a href="/{{ $p->slug }}" class="drawer_link {{ request()->is($p->slug) || request()->is($p->slug.'/*') ? 'is_active' : '' }}">{{ $p->name }}</a>
              @endif
            @endforeach
          </div>
        </div>

        <div class="drawer_group">
          <button class="drawer_link drawer_parent {{ request()->routeIs('*dai_ly*') ? 'is_active' : '' }}" type="button">
            Đại lý <span class="drawer_caret">▾</span>
          </button>
          <div class="drawer_sub">
            <a href="{{ clientRoute('chinh_sach_dai_ly') }}" class="drawer_link {{ request()->routeIs('chinh_sach_dai_ly') ? 'is_active' : '' }}">Chính sách</a>
            <a href="{{ clientRoute('auth.register') }}" class="drawer_link {{ request()->routeIs('auth.register') ? 'is_active' : '' }}">Đăng ký</a>
          </div>
        </div>

        <a href="{{ clientRoute('tin_tuc') }}" class="drawer_link {{ request()->routeIs('*tin_tuc*') ? 'is_active' : '' }}">Tin tức</a>
        <a href="{{ clientRoute('contact.index') }}" class="drawer_link {{ request()->routeIs('*contact*') ? 'is_active' : '' }}">Liên hệ</a>
      </nav>
    </div>
  </aside>
</header>
