@extends ('master')
@section('title', 'Daftar User Biasa')
@section('content')
<div class="card">
  <div class="card-header">


    <h4></h4>
    <div class="card-header-form">
      <form action="" class="form-inline" method="get">
        @csrf
        <select name="by" class="custom-select custom-select-sm form-control">
        <option value="">All</option>
          @foreach($list as $lis)
        <option value="{{$lis->id}}">{{$lis->name}}</option>
       @endforeach
        </select>
        &nbsp
        <input type="text" class="form-control" placeholder="search" name="search" >
        &nbsp
        <button type="submit" class="btn btn-primary"><span class="fas fa-check"></span></button>  
      
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
             <td>              <form method="post" action="user/delete/{{$gets->id}}"> 
              @csrf
               <input type="hidden" name="_method" value="DELETE">
 
               @if ($gets->active == 0)
               <a href="/user/act/{{$gets->id}}" onclick="return confirm('Are you sure?')" class="btn btn-success">Activate</a>
               @else
               <a href="/user/non/{{$gets->id}}" onclick="return confirm('Are you sure?')" class="btn btn-secondary">NonActive</a>
               @endif
               
               <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger">delete</button>
               </form>
              </td>
          </tr>
          
          @endforeach        
        

      </table>
      <div class="float-right">
      <!-- {{ $get->links() }} -->
    </div>
    </div>
  </div>
</div>
</div>
              @endsection