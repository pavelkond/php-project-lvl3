@extends('layouts.app')

@section('content')
    <div class="form-wrapper">
        <h1>Анализатор страниц</h1>
        <h2>Бесплатно проверяйте сатый на SEO пригодность</h2>
        <form action="{{ route('urls.store') }}" method="post">
            @csrf
            <input type="text" name="url[name]" value="{{ session('currentUrl') }}"
                   placeholder="https://www.expample.com">
            <input type="submit" value="Проверить">
        </form>
    </div>
@endsection
