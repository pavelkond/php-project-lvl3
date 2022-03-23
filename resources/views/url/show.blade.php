@extends('layouts.app')

@section('content')
    <h1>Сайт: {{ $url->name }}</h1>
    <table>
        <tr>
            <th>ID</th>
            <td>{{ $url->id }}</td>
        </tr>
        <tr>
            <th>Имя</th>
            <td>{{ $url->name }}</td>
        </tr>
        <tr>
            <th>Дата создания</th>
            <td>{{ $url->created_at }}</td>
        </tr>
    </table>
    <div>
        <h2>Проверки</h2>
        <form action="{{ route('urls.check', $url->id) }}" method="post">
            @csrf
            <input type="submit" value="Запустить проверку">
        </form>
        <table>
            <tr>
                <th>ID</th>
                <th>Код ответа</th>
                <th>h1</th>
                <th>title</th>
                <th>description</th>
                <th>Дата создания</th>
            </tr>
            @foreach ($checks as $check)
                <tr>
                    <td>{{ $check->id }}</td>
                    <td>{{ $check->status_code }}</td>
                    <td>{{ $check->h1 }}</td>
                    <td>{{ $check->title }}</td>
                    <td>{{ $check->description }}</td>
                    <td>{{ $check->created_at }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
