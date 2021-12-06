@extends ('master')
@section('title', 'Daftar Fasilitas')
@section('content')
<div class="card">
  <div class="card-header">
<a href="/fasilitas/input" class="btn btn-success mr-2"><i class="fas fa-plus"></i><span>Tambah User</span></a>
    <h4></h4>
    <div class="card-header-form">
      <form action="" class="form-inline" method="get">
        <div class="input-group">
          <input type="text" class="form-control" name="search" placeholder="Search">
          <div class="input-group-btn">
         
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
          </div>
        </div>
        <a href="/fasilitas" class="btn btn-info ml-2">All</a>
      </form>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped">
        <tr>
          <th>No</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
        @php
            $no = 0;
        @endphp
          @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            
             <td>{{$gets->name}} </td>
             <td>
             &nbsp
              <form method="post" action="/fasilitas/delete/{{$gets->id}}"> 
             @csrf
              <input type="hidden" name="_method" value="DELETE">
              <a href="/fasilitas/edit/{{$gets->id}}" onclick="return confirm('Are you sure?')" class="btn btn-warning">Edit</a>
              <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">delete</button>
              </form>
             
             </td>
          </tr>
          
          @endforeach        
        

      </table>
      <div class="float-right">
      {{ $get->links() }}
    </div>
    </div>
  </div>
</div>
</div>
              @endsection