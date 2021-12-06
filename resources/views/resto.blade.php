@extends ('master')
@section('title', 'Daftar Restoran')
@section('content')
<div class="card">
  <div class="card-header">
    <h4></h4>
    <div class="card-header-form">
      <form action="" class="form-inline" method="get">
        <select name="by" class="custom-select custom-select-sm form-control">
          <option value="">All</option>
          <option value="name">Nama</option>
          <option value="address">Jalan</option>
          <option value="users_id">Pemilik</option>
          </select>
        <div class="input-group">
          <input type="text" class="form-control" name="search" placeholder="Search">
          <div class="input-group-btn">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped">
        <tr>
          <th>No</th>
          <th>Name</th>
          <th>Alamat</th>
          <th>Action</th>
        </tr>
        @php
            $no = 0;
        @endphp
        
        @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            
             <td>{{$gets->name}} </td>
             <td>{{$gets->address}}</td>
             <td>
              <form method="post" action="resto/delete/{{$gets->id}}"> 
                @csrf
                 <input type="hidden" name="_method" value="DELETE">
                 <a href="/analisis/resto/{{$gets->id}}" class="btn btn-primary">Cek</a>
                 @if ($gets->status == 'active')
                 <a href="/resto/non/{{$gets->id}}" onclick="return confirm('Are you sure?')" class="btn btn-secondary">NonActive</a>

                @else
                <a href="/resto/act/{{$gets->id}}" onclick="return confirm('Are you sure?')" class="btn btn-success">Activate</a>
                @endif
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