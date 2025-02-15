<!doctype html>
<html class="no-js h-100" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Publyse - Verified Publication</title>
    <meta name="description" content="A high-quality &amp; application as your book journey.">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" id="main-stylesheet" data-version="1.1.0" href="{{asset('template/styles/shards-dashboards.1.1.0.min.css')}}">
    <link rel="stylesheet" href="{{asset('template/styles/extras.1.1.0.min.css')}}">
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Datatable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>


  </head>
  <body class="h-100">
    <div class="container-fluid">
      <div class="row">
        <!-- Main Sidebar -->
        <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
          <div class="main-navbar">
            <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
              <a class="navbar-brand w-100 mr-0" href="#" style="line-height: 25px;">
                <div class="d-table m-auto">
                  <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 25px;" src="{{asset('template/images/shards-dashboards-logo.svg')}}" alt="Shards Dashboard">
                  <span class="d-none d-md-inline ml-1">Publyse Dashboards</span>
                </div>
              </a>
              <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                <i class="material-icons">&#xE5C4;</i>
              </a>
            </nav>
          </div>
          <form action="#" class="main-sidebar__search w-100 border-right d-sm-flex d-md-none d-lg-none">
            <div class="input-group input-group-seamless ml-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-search"></i>
                </div>
              </div>
              <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search"> </div>
          </form>
          <div class="nav-wrapper">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link {{ Request::is('act')? "active":"" }}" href="{{ url('/act')}}">
                  <i class="material-icons">view_module</i>
                  <span>Tampilan Pemeriksaan Keseluruhan</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link {{ Request::is('publikasi')? "active":"" }}" href="{{ url('/publikasi')}}">
                  <i class="material-icons">assessment</i>
                  <span>Master Publikasi</span>
                </a>
              </li>  

              <li class="nav-item">
                <a class="nav-link {{ Request::is('assignment')? "active":"" }}" href="{{ url('/assignment')}}">
                  <i class="material-icons">assessment</i>
                  <span>Alokasi Pemeriksaan</span>
                </a>
              </li>     

              <li class="nav-item">
                <a class="nav-link {{ Request::is('pemeriksaan')? "active":"" }}" href="{{ url('/pemeriksaan')}}">
                  <i class="material-icons">assessment</i>
                  <span>Pemeriksaan Konten</span>
                </a>
              </li>      

              <li class="nav-item">
                <a class="nav-link {{ Request::is('pemeriksaan')? "active":"" }}" href="{{ url('/pemeriksaan/create2')}}">
                  <i class="material-icons">assessment</i>
                  <span>Pemeriksaan Non-Konten</span>
                </a>
              </li>     

              <li class="nav-item">
                <a class="nav-link {{ Request::is('//masternonkonten')? 'active':'' }}" href="{{ url('/masternonkonten')}}">
                  <i class="material-icons">person</i>
                  <span>Master Non Konten</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link {{ Request::is('//user/profile')? 'active':'' }}" href="https://api.whatsapp.com/send/?phone=6285265513571&text=%22Mohon%20bantuannya%20terkait%20...%22&app_absent=0">
                  <i class="material-icons">hail</i>
                  <span>Bantuan</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link " href="{{ url('/logout')}}">
                  <i class="material-icons">error</i>
                  <span>Logout</span>
                </a>
              </li>
            </ul>
          </div>
        </aside>
        <!-- End Main Sidebar -->
        <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
          <div class="main-navbar sticky-top bg-white">
            <!-- Main Navbar -->
            <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0">
              <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
                <div class="input-group input-group-seamless ml-3 d-flex align-items-center">
                  <div>
                    <img src="{{asset('template/images/avatars/bps3.png')}}" alt="User Avatar" style="width:150px;height:30px;">
                  </div>
                </div>
              </form>
              <ul class="navbar-nav border-left flex-row align-items-center">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-nowrap px-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="user-avatar rounded-circle mr-2" src="{{asset('template/images/avatars/bps.png')}}" alt="User Avatar">
                    <span class="d-none d-md-inline-block">{{ Auth::user()->fullname }}</span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="#">
                      <i class="material-icons">&#xE7FD;</i> Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ url('/logout')}}">
                      <i class="material-icons text-danger">&#xE879;</i> Logout </a>
                  </div>
                </li>
              </ul>
              <nav class="nav">
                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                  <i class="material-icons">&#xE5D2;</i>
                </a>
              </nav>
            </nav>
          </div>
          <!-- / .main-navbar -->
          <div class="main-content-container container-fluid px-4">

            @yield('content')

          </div>
          <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
            <ul class="nav">
              <li class="nav-item">
                <a class="nav-link" href="#">Versi 1.0.0</a>
              </li>
            </ul>
            <span class="copyright ml-auto my-auto mr-2">Copyright ©2024-<?php echo date("Y"); ?>
              <a href="https://padangpanjangkota.bps.go.id" rel="nofollow">BPS Kota Padang Panjang</a>
            </span>
          </footer>
        </main>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/shards-ui@latest/dist/js/shards.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sharrre/2.0.1/jquery.sharrre.min.js"></script>
    <script src="{{asset('template/scripts/extras.1.1.0.min.js')}}"></script>
    <script src="{{asset('template/scripts/shards-dashboards.1.1.0.min.js')}}"></script>
    <script src="{{asset('template/scripts/app/app-blog-overview.1.1.0.js')}}"></script>


@stack('scripts')
  </body>
</html>
