@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('content')
<x-cards header='Lista de Clientes' titlecolor='primary'>
    <table class="table table-sm mb-0 table-bordered table-hover">
        <thead class="table-dark text-uppercase">
            <tr >
                <th>Nombre</th>
                <th>Prefix</th>
            </tr>
        </thead>
        {{-- PARA METODOS LARAVEL --}}
        {{-- <tbody>
            @foreach ($data as $d)
                <tr onclick="window.location='/clients/{{ $d->id }}';">
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->prefix }}</td>
                </tr>
            @endforeach
        </tbody> --}}



        {{-- PARA METODOS API --}}
        <tbody>
            @foreach ($data as $d)
                <tr style="cursor: pointer" onclick="window.location='/clients/{{ $d['id'] }}';">
                    <td>{{ $d['name'] }}</td>
                    <td>{{ $d['prefix'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-cards>
@endsection
