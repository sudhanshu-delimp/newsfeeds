@extends('layouts.admin')
@push('child-style')
<link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{$pageHeading}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">{{$pageHeading}}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{$pageHeading}}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                @if($message = Session::get('success'))
                <div class="alert alert-success">
                <p>{{ $message }}</p>
                </div>
                @endif
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Sn.</th>
                    <th>Site</th>
                    <th>Title</th>
                    <th>Created</th>
                    <th>Live Link</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if(!$posts->isEmpty())
                        @foreach($posts as $key=>$post)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$post->site->title}}</td>
                                <td>{{$post->title}}</td>
                                <!-- <td>{{ \Carbon\Carbon::parse($post->publish_date)->format('Y-m-d') }}</td> -->
                                <td>{{ \Carbon\Carbon::parse($post->created_at)->format('Y-m-d h:i A') }}</td>
                                <td>
                                  <a target="_blank" href="{{$post->live_link}}">View</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                  </tbody>
                  <tfoot>
                  <tr>
                <th>Sn.</th>
                <th>Title</th>
                <th>Site</th>
                <th>Created</th>
                <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@stop
@push('child-scripts')
<script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endpush