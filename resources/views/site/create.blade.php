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
              <form method="post" action="{{ route('manage_site.store')}}" enctype="multipart/form-data" class="form">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" id="Title" placeholder="Enter Title">
                    @error('title')
                        <span class="text-danger" role="alert">
                            <strong>@lang(strtolower($message))</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Site</label>
                    <input type="text" name="site" value="{{ old('site') }}" class="form-control" id="Site" placeholder="Enter Site">
                    @error('site')
                        <span class="text-danger" role="alert">
                            <strong>@lang(strtolower($message))</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="exampleInputFile">Logo</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text">Upload</span>
                      </div>
                    </div>
                    @error('file')
                        <span class="text-danger" role="alert">
                            <strong>@lang(strtolower($message))</strong>
                        </span>
                      @enderror
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <input class="btn btn-primary" id="btnSubmit" name="add" type="submit" value="Submit">
                </div>
              </form>
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

<script>
  $(function () {
    
  });
</script>
@endpush