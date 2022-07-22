<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Alcoll</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ session()->get('account')->name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item  {{ $controller == 'SiteController'?'menu-is-opening menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Sites
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('manage_site.create')}}" class="nav-link {{ $action == 'create' && $controller == 'SiteController'?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Site</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('manage_site.index')}}" class="nav-link {{ $action == 'index' && $controller == 'SiteController'?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Site</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item  {{ $controller == 'FeedController'?'menu-is-opening menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Feeds
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('manage_feed.create')}}" class="nav-link {{ $action == 'create' && $controller == 'FeedController'?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Feed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('manage_feed.index')}}" class="nav-link {{ $action == 'index' && $controller == 'FeedController'?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Feed</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item  {{ $controller == 'PostController'?'menu-is-opening menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-envelope"></i>
              <p>
                Posts
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('manage_post.index')}}" class="nav-link {{ $action == 'index' && $controller == 'PostController'?'active':'' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Post</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{route('logout')}}" class="nav-link">
              <i class="nav-icon fas fa-columns"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
