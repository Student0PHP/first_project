@extends('layoutsfororder.reference-stats')

@section('title', 'Статистика заказов')


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 8px;
        word-break: break-word;
    }
</style>


@section('content')
    <h1 class="text-white">Статистика по выполненным заказам за последние 7 дней</h1>


    <table class="table table-bordered" >
        <thead>
        <tr class="text-white">
            <th scope="col">#</th>
            <th scope="col">Дата</th>
            <th scope="col">Доход</th>
            <th scope="col">Общее время</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($stats as $index => $data)
            <tr class="text-white">
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $index }}</td>
                <td>{{ number_format($data['earnings'], 2) }}</td>
                <td>{{ $data['formatted_total_time'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
