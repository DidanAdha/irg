@extends ('master')
@section('title', 'Tulis Feedback')
@section('content')
<div class="card">
    <div class="card-header">

        <h4></h4>

        
            <form action="" method="post" class="form-group col-md-12 ">
                <label for=""></label>

                <textarea name="konten" class="form-controll " id="konten"></textarea>
            </div>
            </form>
        
    
    <div class="card-body p-0">
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