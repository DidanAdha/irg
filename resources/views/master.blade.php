<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>IRG &mdash; ADMIN</title>

  <!-- General CSS Files -->
  @include('partials.css')
  <script type="text/javascript" src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      @include('partials.navbar')
      <div class="main-sidebar">
        @include('partials.sidebar')
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>@yield('title')</h1>
          </div>
          <div class="section-body">
            @yield('content')
          </div>
        </section>
      </div>

    </div>
  </div>

  <!-- General JS Scripts -->
  @include('partials.js')
</body>
@stack('js')
</html>