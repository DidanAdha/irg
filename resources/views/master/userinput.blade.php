@extends ('master')
@section('title', 'Input Admin')
@section('content')
<div class="card">
  <div class="card-header">

    <h4></h4>
    <div class="card-header-form">
      <form action="" method="post">
      </form>
      </div>
    </div>
  
  <div class="card-body p-0  align-content-center">

  <form method="post" action="/user/insert" >
  @csrf
  <center>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Name</label>
    <div class="col-sm-6">
      <input type="text" class="form-control col-md-6" id="staticEmail" placeholder="Input name" name="name">
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-6">
      <input type="email" class="form-control col-md-6" id="staticEmail" placeholder="Input email" name="email">
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Address</label>
    <div class="col-sm-6">
      <input type="text" class="form-control col-md-6" id="staticEmail" placeholder="Input address" name="address">
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Date of birth</label>
    <div class="col-sm-6">
      <input type="date" class="form-control col-md-6" id="staticEmail" placeholder="Input nama" name="ttl">
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Role</label>
    <div class="col-sm-6">
      <select name="roles_id" class="custom-select custom-select-sm col-md-6 form-control">
        <option value="">Input Role</option>
          @foreach($lili as $lis)
        <option value="{{$lis->id}}">{{$lis->name}}</option>
       @endforeach
        </select>
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Phone</label>
    <div class="col-sm-6">
      <input type="text" class="form-control col-md-6" id="staticEmail" placeholder="Input phone number" name="phone_number">
    </div>
  </div>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-6">
      <input type="text" class="form-control col-md-6" id="staticEmail" placeholder="Masukkan nama" name="password">
    </div>
  </div>
  <a href="useradmin" class="btn btn-danger">Cancel</a>
  <button type="submit" class="btn btn-primary">Submit</button>
  </center>
</form>
<br><br>
  </div>
</div>

@endsection