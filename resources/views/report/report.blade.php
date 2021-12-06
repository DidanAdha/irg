@extends ('master')
@section('title', 'Report')
@section('content')
<div class="card">
  <div class="card-header">
    <h4></h4>
    <div class="card-header-form">
      <form action="" class="form-inline" method="get">
        <div class="input-group">
          <input type="text" class="form-control" name="search" placeholder="Search">
          <div class="input-group-btn">
         
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
          </div>
        </div>
        <a href="/report" class="btn btn-info ml-2">All</a>
      </form>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped">
        <tr>
          <th>No</th>
          <th>Restaurants</th>
          <th>Report</th>
          <th>Action</th>
        </tr>
        @php
            $no = 0;
        @endphp
          @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            
             <td>{{$gets->restaurants_id}}</td>
             <td>{{$gets->total}}</td>
             <td><a href="/report/{{$gets->restaurants_id}}" class="btn btn-primary">Cek</a></td>
          </tr>
          
          @endforeach        
        

      </table>
      <div class="float-right">
    </div>
    </div>
  </div>
</div>
</div>
              @endsection