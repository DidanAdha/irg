@extends ('master')
@section('title', 'Daftar Restoran')
@section('content')
<div class="card">
  <div class="card-header">
    <div class="dropdown d-inline mr-2">
      <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Easy Dropdown
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <a class="dropdown-item" href="#">Something else here</a>
      </div>
    </div>

    <h4></h4>
    <div class="card-header-form">
      <form>
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search">
          <div class="input-group-btn">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
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
          <th>Pemilik</th>
          <th>Action</th>
        </tr>
        @php
            $no = 0;
        @endphp
          @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            
             <td>{{$gets->name}} </td>
             <td>{{$gets->id}}</td>
             <td>{{$gets->user_id}}</td>
             <td><a href="/analisis/resto/{{$gets->id}}" class="btn btn-primary">Cek</a></td>
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