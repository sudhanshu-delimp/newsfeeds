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
                <h3 class="card-title">{{'search'}}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form id="search-form">
                  <div class="row">
                    <div class="form-group col-sm-3">
                      <label for="Name">Site</label>
                      <select  class="form-control" name="site"  id="search_site">
                        <option value="">All</option>
                        @if(!$sites->isEmpty())
                          @foreach($sites as $site)
                            <option value="{{$site->id}}">{{$site->title}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="Email">Title</label>
                      <input class="form-control" name="title" type="text" id="search_title" placeholder="Title">
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="From_Date">From Date</label>
                      <input class="form-control" name="from_date" type="text" id="search_startdate" placeholder="From Date" data-date-format="yyyy-mm-d">
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="To_Date">To Date</label>
                      <input class="form-control" name="to_date" type="text" id="search_enddate" placeholder="To Date" data-date-format="yyyy-mm-d">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-sm-12 text-center">
                      <a href="#" class="btn btn-primary" id="search">Search</a>
                      <a href="#" class="btn btn-primary" id="reset">Reset</a>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
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
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Sn.</th>
                    <th>Site</th>
                    <th>Title</th>
                    <th>Created</th>
                    <th>Live Link</th>
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
  $(function(){
      var table = $('#example2').DataTable({
          'pageLength':20,
          'lengthChange': false,
          'searching'   : false,
          "processing": true,
          "serverSide": true,
          "order": [[ 3, "desc" ]],
          "columnDefs": [{
          "targets": [1],
          "orderable": false
          }],
          "ajax":{
                   url: "{{ route('getposts') }}",
                   dataType: "json",
                   type: "POST",
                   data:function(data) {
                    data.site = $('#search_site').val();
                    data.title = $('#search_title').val();
                    data.from_date = $('#search_startdate').val();
                    data.to_date = $('#search_enddate').val();
                  }
                  // ,
                  // success: function(data){
                  //   console.log(data);
                  // }
                 },
          "columns": [
              { "data": "sn" },
              { "data": "site" },
              { "data": "title" },
              { "data": "created" },
              { "data": "live_link" }
          ]

      });
      $('#search').on('click', function (event) {
        event.preventDefault();
        table.draw();
      });
      $('#reset').on('click', function(event){
        event.preventDefault();
        $("#search-form")[0].reset();
        table.draw();
      });

      var dateFormat = "yy-mm-dd",
      from = $( "#search_startdate" )
        .datepicker({
          defaultDate: "+1w",
          dateFormat:dateFormat,
          changeMonth: true,
          numberOfMonths: 1
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#search_enddate" ).datepicker({
        defaultDate: "+1w",
        dateFormat:dateFormat,
        changeMonth: true,
        numberOfMonths: 1
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
      return date;
    }



    })
</script>
@endpush
