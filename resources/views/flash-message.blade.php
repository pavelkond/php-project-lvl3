@if($message = session('error'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = session('success'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = session('warning'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif
