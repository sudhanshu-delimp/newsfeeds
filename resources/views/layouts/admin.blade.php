<!DOCTYPE html>
<html>
<head>
  @include('includes.head')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  @include('includes.header')
  @include('includes.sidebar')
<div class="content-wrapper">
  @yield('content')
</div>
  @include('includes.footer')
  <!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<script src="{{ asset('/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/dist/js/adminlte.min.js') }}"></script>
<script>
  var base_url = '{{ url('') }}';
  $.ajaxSetup({
    headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
</script>
@stack('child-scripts')
</body>
</html>
