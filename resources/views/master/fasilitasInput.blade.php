@extends ('master')
@section('title', 'Input Fasilitas')
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

  <form method="post" action="/fasilitas/insert" >
  @csrf
  <center>
  <div class="form-group row mx-auto">
    <label for="staticEmail" class="col-sm-2 col-form-label">Fasilitas</label>
    <div class="col-sm-6">
      <input type="text" class="form-control col-md-6" id="staticEmail" placeholder="Masukkan nama" name="name">
    </div>
  </div>
  <a href="/fasilitas" class="btn btn-danger">Cancel</a>
  <button type="submit" class="btn btn-primary">Submit</button>
  </center>
</form>
<br><br>
  </div>
</div>

              @endsection