@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('content')
    <x-cards header='Lista de Clientes' titlecolor='primary'>

        <table class="table table-sm mb-0 table-bordered table-hover">
            <thead class="table-dark text-uppercase text-center">
                <tr>
                    <th class="w-50">Nombre</th>
                    <th>Prefix</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @if (count($data) > 0)
                    @foreach ($data as $d)
                        <tr style="cursor: pointer" onclick="window.location='/generate/{{ $d['id'] }}';">
                            <td>{{ $d['name'] }}</td>
                            <td>{{ $d['prefix'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr class="text-center">
                        <td colspan="2">
                            <div class="alert alert-danger mb-0" role="alert">nada que mostrar</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </x-cards>
@endsection
