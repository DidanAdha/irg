@extends ('master')
@section('title', 'Daftar Feedback')
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
        <option value="{{$lis->status}}">{{$lis->status}}</option>
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
          <th>Deskripsi</th>
          <th>Replay</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        @php
            $no = 0;
        @endphp
          @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            <td>{{ $gets->users_id }}</td>
             <td>{!! \Illuminate\Support\Str::words($gets ->desc, 15,'...')  !!}</td>
             <td>{!! \Illuminate\Support\Str::words($gets ->message, 15,'...')  !!}</td>
          <td>{{$gets->status}}</td>
             <td>
               @if (($gets->status) === 'pending')
               <a href="feedback_user/{{$gets->id}}" class="btn btn-success">Read</a></td>    
               @else
               <a href="feedback_user/{{$gets->id}}" class="btn btn-primary">Cek</a></td>
               @endif
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