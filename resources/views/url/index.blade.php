@extends('layouts.app')

@section('content')
    <h1>Сайты</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Последняя проверка</th>
            <th>Код ответа</th>
        </tr>
        @foreach ($urls as $url)
            <tr>
                <th>{{ $url->id }}</th>
                <th><a href="{{ route('urls.show', $url->id) }}">{{ $url->name }}</a></th>
                <th></th>
                <th></th>
            </tr>
        @endforeach
    </table>
@endsection
