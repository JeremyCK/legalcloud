<div class="c-wrapper">
  <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
    <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show"><span class="c-header-toggler-icon"></span></button>
    <a class="c-header-brand d-sm-none" href="#">
      <!-- <img class="c-header-brand-full c-d-dark-none" src="{{ url('/assets/brand/logo-lhyeo.jpeg') }}" width="auto" height="46" alt="CoreUI Logo"> -->
      <!-- <img class="c-header-brand-minimized c-d-dark-none" src="{{ url('/assets/brand/coreui-signet.svg') }}" width="46" height="46" alt="CoreUI Logo"> -->
      <!-- <img class="c-header-brand-full c-d-light-none" src="{{ url('/assets/brand/logo-lhyeo.jpeg') }}" width="118" height="46" alt="CoreUI Logo"> -->
      <!-- <img class="c-header-brand-minimized c-d-light-none" src="{{ url('/assets/brand/coreui-signet-white.svg') }}" width="46" height="46" alt="CoreUI Logo"> -->
    </a>
    <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true"><span class="c-header-toggler-icon"></span></button>

    <ul class="c-header-nav ml-auto">
      <!-- <li class="c-header-nav-item">
              <form id="select-locale-form" action="/locale" method="GET">
                <select name="locale" id="select-locale" class="form-control">
                    @foreach($locales as $locale)
                        @if($locale->short_name == $appLocale)
                            <option value="{{ $locale->short_name }}" selected>{{ $locale->name }}</option>
                        @else
                            <option value="{{ $locale->short_name }}">{{ $locale->name }}</option>
                        @endif
                    @endforeach
                </select>
              </form>
          </li> -->
      <!-- <li class="c-header-nav-item px-3">
            <button class="c-class-toggler c-header-nav-btn" type="button" id="header-tooltip" data-target="body" data-class="c-dark-theme" data-toggle="c-tooltip" data-placement="bottom" title="Toggle Light/Dark Mode">
              <svg class="c-icon c-d-dark-none">
                <use xlink:href="{{ url('/icons/sprites/free.svg#cil-moon') }}"></use>
              </svg>
              <svg class="c-icon c-d-default-none">
                <use xlink:href="{{ url('/icons/sprites/free.svg#cil-sun') }}"></use>
              </svg>
            </button>
          </li> -->
    </ul>


    <ul class="c-header-nav">

    {{-- <li class="c-header-nav-item dropdown d-md-down-none mx-2"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-chat-bubble') }}"></use>
          </svg>
          <div class="notification-message-badge"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0">
          <div class="dropdown-header bg-light"><strong>You have <span id="notification_message_count">0</span> notifications</strong></div>
          <div id="notification_messsage_list" style="overflow-y:auto;max-height:200px">
            <a class="dropdown-item" href="javascript:void(0)">
              <svg class="c-icon mr-2 text-success">
                <use xlink:href="javascript:void(0)"></use>
              </svg>No notification</a>
          </div>
        </div>
      </li>

      <li class="c-header-nav-item dropdown d-md-down-none mx-2"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-file') }}"></use>
          </svg>
          <div class="notification-receipt-badge"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0">
          <div class="dropdown-header bg-light"><strong>You have <span id="notification_receipt_count">0</span> notifications</strong></div>
          <div id="notification_receipt_list" style="overflow-y:auto;max-height:200px">
            <a class="dropdown-item" href="javascript:void(0)">
              <svg class="c-icon mr-2 text-success">
                <use xlink:href="javascript:void(0)"></use>
              </svg>No notification</a>
          </div>
        </div>
      </li> --}}

      <li class="c-header-nav-item dropdown d-md-down-none mx-2"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {{-- <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-bell') }}"></use>
          </svg> --}}
          
          <i class="c-icon cil-bell"></i>
          <div class="notification-badge"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0">
          <div class="dropdown-header bg-light"><strong>You have <span id="notification_count">0</span> notifications</strong></div>
          <div id="notification_list" style="overflow-y:auto;max-height:200px">
            <a class="dropdown-item" href="javascript:void(0)">
              <svg class="c-icon mr-2 text-success">
                <use xlink:href="javascript:void(0)"></use>
              </svg>No notification</a>
          </div>


          {{-- <a class="dropdown-item" href="#">
          <svg class="c-icon mr-2 text-success">
              <use xlink:href="{{ url('/icons/sprites/free.svg#cil-user-follow') }}"></use>
            </svg> New user registered</a>
             <a class="dropdown-item" href="#">
            <svg class="c-icon mr-2 text-danger">
              <use xlink:href="{{ url('/icons/sprites/free.svg#cil-user-unfollow') }}"></use>
            </svg> User deleted</a><a class="dropdown-item" href="#">
            <svg class="c-icon mr-2 text-info">
              <use xlink:href="{{ url('/icons/sprites/free.svg#cil-chart') }}"></use>
            </svg> Sales report is ready</a><a class="dropdown-item" href="#">
            <svg class="c-icon mr-2 text-success">
              <use xlink:href="{{ url('/icons/sprites/free.svg#cil-basket') }}"></use>
            </svg> New client</a><a class="dropdown-item" href="#">
            <svg class="c-icon mr-2 text-warning">
              <use xlink:href="{{ url('/icons/sprites/free.svg#cil-speedometer') }}"></use>
            </svg> Server overloaded</a>
          <div class="dropdown-header bg-light"><strong>Server</strong></div><a class="dropdown-item d-block" href="#">
            <div class="text-uppercase mb-1"><small><b>CPU Usage</b></small></div><span class="progress progress-xs">
              <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </span><small class="text-muted">348 Processes. 1/4 Cores.</small>
          </a><a class="dropdown-item d-block" href="#">
            <div class="text-uppercase mb-1"><small><b>Memory Usage</b></small></div><span class="progress progress-xs">
              <div class="progress-bar bg-warning" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
            </span><small class="text-muted">11444GB/16384MB</small>
          </a><a class="dropdown-item d-block" href="#">
            <div class="text-uppercase mb-1"><small><b>SSD 1 Usage</b></small></div><span class="progress progress-xs">
              <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
            </span><small class="text-muted">243GB/256GB</small>
          </a>  --}}
        </div>
      </li>



      <li class="c-header-nav-item dropdown"><a class="c-header-nav-link hide" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <div class="c-avatar"><img class="c-avatar-img" src="/assets/img/avatars/img-default-profile.png" alt="user@email.com"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-right pt-0">
          <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>
          <a class="dropdown-item" href="javascript:void(0)">
            
            <i class="cil cil-user c-sidebar-nav-icon"></i>
            {{ $current_user->name }}</a>
            <a class="dropdown-item" href="change_password">
              <i class="cil cil-settings c-sidebar-nav-icon"></i>
              Change Password</a>
            </a>
            
          
          <a class="dropdown-item" href="#">
            <i class="cil cil-account-logout c-sidebar-nav-icon"></i>
            <form action="{{ url('/logout') }}" method="POST"> @csrf <button type="submit" class="btn btn-ghost-dark btn-block">Logout</button></form>
          </a>
        </div>
      </li>

    </ul>




    <div class="c-subheader px-3" style="display:none;">
      <!-- Breadcrumb-->
      <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <?php $segments = ''; ?>
        @for($i = 1; $i <= count(Request::segments()); $i++) <?php $segments .= '/' . Request::segment($i); ?> @if($i < count(Request::segments())) <li class="breadcrumb-item">{{ Request::segment($i) }}</li>
          @else
          <li class="breadcrumb-item active">{{ Request::segment($i) }}</li>
          @endif
          @endfor
          <!-- Breadcrumb Menu-->
      </ol>
      <div class="c-header-nav ml-auto d-md-down-none mr-2"><a class="c-header-nav-link" href="#">
          <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-speech') }}"></use>
          </svg></a><a class="c-header-nav-link" href="#">
          <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-graph') }}"></use>
          </svg>  Dashboard</a><a class="c-header-nav-link" href="#">
          <svg class="c-icon">
            <use xlink:href="{{ url('/icons/sprites/free.svg#cil-settings') }}"></use>
          </svg>  Settings</a></div>
    </div>
  </header>