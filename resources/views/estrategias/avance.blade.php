@extends('layouts.app')

@section('content')


{{-- {{ var_dump($data) }} --}}

<x-cards titlecolor='primary'>
    <table class="table table-sm mb-0 table-bordered table-hover">
        <thead class="table-dark text-uppercase text-center">
            <tr>
                <th>prefix</th>
                <th>rut</th>
                <th>email</th>
                <th>monto</th>
                <th>estado</th>
                <th>fecha de envio</th>
                <th>leido</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach ($datas as $key => $data)
            <tr>
                <td>{{ $data['prefix'] }}</td>
                <td>{{ $data['rut'] }}</td>
                <td>{{ $data['email'] }}</td>
                <td>{{ $data['monto'] }}</td>
                <td>{{ $arrrr2[$key]['estado'] }}</td>
                <td>{{ date('d-m-Y', strtotime($data['fecha'])) }}</td>
                <td>{{ date('d-m-Y', strtotime($arrrr2[$key]['fecha_update'])) }}</td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</x-cards>
@endsection