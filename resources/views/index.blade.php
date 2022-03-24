@extends('layouts.app')

@section('content')
    <div class="container-lg mt-3">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                <h1 class="display-3">Анализатор страниц</h1>
                <p class="lead">Бесплатно проверяйте сатый на SEO пригодность</p>
                <form action="{{ route('urls.store') }}" method="post" class="d-flex justify-content-center">
                    @csrf
                    <input type="text" name="url[name]" value="{{ session('currentUrl') }}" class="form-control form-control-lg"
                           placeholder="https://www.expample.com">
                    <input type="submit" value="Проверить" class="btn-primary btn btn-lg ms-3 px-5 text-uppercase mx-3">
                </form>
            </div>
        </div>
    </div>
@endsection
