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
@endsection
