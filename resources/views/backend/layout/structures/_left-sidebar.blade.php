<aside class="left-sidebar" data-sidebarbg="skin5">
  <div class="scroll-sidebar">
    <nav class="sidebar-nav">
      <ul id="sidebarnav" class="p-t-30">
        <li class="sidebar-item {{ selectedMenu('stats') }}">
          <a class="sidebar-link waves-effect waves-dark {{ activeMenu('stats') }}" href="{{ backendRoute('stats') }}">
            <i class="mdi mdi-chart-bar"></i>
            <span class="hide-menu">Thống kê</span>
          </a>
        </li>

        <li class="sidebar-item {{ selectedMenu('category') }}">
          <a class="sidebar-link waves-effect waves-dark {{ activeMenu('category') }}" href="{{ backendRoute('category.index') }}">
            <i class="mdi mdi-folder"></i>
            <span class="hide-menu">Danh mục</span>
          </a>
        </li>

        <li class="sidebar-item {{ selectedMenu('post') }}">
          <a class="sidebar-link waves-effect waves-dark {{ activeMenu('post') }}" href="{{ backendRoute('post.index') }}">
            <i class="mdi mdi-file-document"></i>
            <span class="hide-menu">Bài viết</span>
          </a>
        </li>

        <li class="sidebar-item {{ selectedMenu('trend-log') }}">
          <a class="sidebar-link waves-effect waves-dark {{ activeMenu('trend-log') }}" href="{{ backendRoute('trend-log.index') }}">
            <i class="mdi mdi-robot"></i>
            <span class="hide-menu">AI Nhận Định</span>
          </a>
        </li>

        <li class="sidebar-item {{ selectedMenu('contact') }}">
          <a class="sidebar-link waves-effect waves-dark {{ activeMenu('contact') }}" href="{{ backendRoute('contact.index') }}">
            <i class="mdi mdi-email"></i>
            <span class="hide-menu">Liên hệ</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark" href="{{ backendRoute('auth.logout') }}">
            <i class="mdi mdi-logout"></i>
            <span class="hide-menu">Đăng xuất</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
