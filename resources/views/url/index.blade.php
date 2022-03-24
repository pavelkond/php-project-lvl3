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
                <td>{{ $url->id }}</td>
                <td><a href="{{ route('urls.show', $url->id) }}">{{ $url->name }}</a></td>
                <td>{{ $latestChecks[$url->id]['latest'] ?? '' }}</td>
                <td>{{ $latestChecks[$url->id]['status'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endsection
