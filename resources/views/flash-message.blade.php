@if($message = session('error'))
    <div class="alert alert-danger" role="alert">
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = session('success'))
    <div class="alert alert-info" role="alert">
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = session('warning'))
    <div class="alert alert-info" role="alert">
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif
