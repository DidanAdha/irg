@extends ('master')
@section('title', 'Feedback')
@section('content')

{{-- ----------email  data--------------- --}}

{{-- ------------------------------------- --}}
<div class="card">
        <div class="card-body">
        <div class="ml-3">
            <h4 class="">From : {{$Get->users_id}}</h4>
        <h4>Message : </h4>
        <p>{{$Get->desc}}</p>
    </div>
        </div>
    </div>
<div class="card">
    <div class="card-header ml-4">
        <br>
        <h3>Write Your Reply</h3>
    </div>
    <div class="card-body">

        <form action="/feedback_user/replay/{{$Get->users_id}}" method="post" class="form-group col-md-12 ">
                
    @csrf
    <input type="text" name="email" hidden value="{{$name->email}}">
<input type="text" name="name" hidden value="{{$name->name}}"> 
<input type="text" name="id" hidden value="{{$Get->id}}"> 
                <textarea name="konten" class="form-controll " id="konten"></textarea>
            
                <br>
                <div class="float-right">
                    <button class="btn btn-primary" type="submit" ><span class="fa fa-paper-plane">   Send</span></button>
                </div>
        
                <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
        </form>
 
</div>

</div>

@endsection
@push('js')
<script>
    var konten = document.getElementById("konten");
    CKEDITOR.replace(konten, {
        language: 'en-gb'
    });
    CKEDITOR.config.allowedContent = true;
</script>
@endpush