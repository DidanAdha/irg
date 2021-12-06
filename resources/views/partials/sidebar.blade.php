<aside id="sidebar-wrapper">



    <div class="sidebar-brand">
      <img src="{{ asset('irg.png') }}" width="50" style="margin-top: 10px;" height="50" alt="logo" class="">
    </div>

    <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
          
            <!-- <li class="active"><a class="nav-link" href="index-0.html"><i class="fas fa-fire"></i><span>Dashboard</span></li> -->
           
            <li class="active"><a href="/home" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li class="menu-header">Panel</li>
            {{-- <li class="active"><a href="/feedback" class="nav-link"><i class="fas fa-envelope"></i> <span>Feedback</span></a></li> --}}
            @if (Auth::user()->roles_id === 1)
            <li class="active"><a href="/analisis" class="nav-link"><i class="fas fa-utensils"></i> <span>Resto</span></a></li>
                              @else
                      
                  @endif
            {{-- <li class="active"><a href="/report" class="nav-link"><i class="fas fa-exclamation"></i> <span>Report</span></a></li> --}}
            <!-- <li class="ml-auto active">
              <a href="/feedback" class="nav-link"><i class="nav-link fas fa-plus"></i><Span>Resto</Span></a>
              </li>
            <li class="ml-auto active">
              <a href="/feedback" class="nav-link"><i class="nav-link fas fa-plus"></i><Span>Feedback</Span></a>
              </li> -->
              {{-- <li class="active nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-user"></i> <span>User</span></a>
                <ul class="dropdown-menu">
                  @if (Auth::user()->roles_id === 1)
                  <li><a class="nav-link" href="/useradmin">User Admin</a></li>
                  @else
                      
                  @endif

                  <li><a class="nav-link" href="/userbiasa">User Biasa</a></li>
                </ul>
              </li> --}}
              <li class="active nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-server"></i> <span>Data Master</span></a>
                <ul class="dropdown-menu">
                  @if (Auth::user()->roles_id === 1)
                  <li><a class="nav-link" href="/role">Role Type</a></li>
                  @else
                      
                  @endif

                  <li><a class="nav-link" href="/menutype">Menu Type</a></li>
                  <li><a class="nav-link" href="/restotype">Resto Type</a></li>
                  <li><a class="nav-link" href="/fasilitas">Fasilitas Type</a></li>
                </ul>
              </li>

              <!-- /////////////////////////////////// -->

      </ul>

      <!-- <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
          <i class="fas fa-rocket"></i> Documentation
        </a>
      </div> -->
      
  </aside>