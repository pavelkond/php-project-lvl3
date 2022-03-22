@if($message = \Illuminate\Support\Facades\Session::get('error'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = Session::get('success'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif

@if($message = Session::get('warning'))
    <div>
        <strong>
            {{ $message }}
        </strong>
    </div>
@endif
