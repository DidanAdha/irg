@extends ('master')
@section('title', 'Report Detail')
@section('content')
<div class="card">
    @foreach ($resto as $reso)
    <div class="card-body">
    <div class="ml-3">
<h4>Restaurant : {{$reso->name}}</h4>
<br>
<h6>Alamat          : {{$reso->address}}</h6>
<h6>Phone Number    : {{$reso->phone_number}}</h6>
</div>
    </div>
</div>
        
@endforeach
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Message</th>

        </tr>
        @php
            $no = 0;
        @endphp
          @foreach($get as $gets)
        
          <tr>
            <td>{{ ++$no }}</td> 
            
             <td>{{$gets->users_id}}</td>
             <td>{{$gets->message}}</td>
            
          </tr>
          
          @endforeach        


      </table>
      <div class="float-left ml-4">
       <a href="/report" class="btn btn-danger">Back</a>
    </div>
      <div class="float-right">
        {{$get->links()}}
    </div>
    </div>
  </div>
</div>
              @endsection