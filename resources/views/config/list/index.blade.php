@extends('layouts.app')
@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-create')
    <x-btn-standar type='a' name='Nueva lista' title='Nueva lista' color="success" sm='sm'
        icon='plus-circle' href="{{ route($config_layout['btn-create'], 'prefix='.$request->prefix) }}" />
        
@endsection

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], $id_cliente) }}" />
@endsection


@section('content')

    <x-cards  header="Listas Activas" titlecolor='primary'>
        <table class="table table-bordered table-sm mb-0">
            <thead class="text-center align-middle bg-dark text-uppercase">
                <tr>
                    <th class="text-white" scope="col">Nombre</th>
                    <th class="text-white" scope="col">discador</th>
                    <th class="text-white" scope="col">IVR</th>
                    <th class="text-white" scope="col" width="280px">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($data as $key => $activo)
                    <tr class="text-uppercase">
                        <td>{{ $activo['nombre'] }}</td>
                        <td>{{ $activo['discador'] }}</td>
                        <td>@if($activo['IVR'] === true) si @else no @endif </td>
                        <td>
                            <x-btn-standar type='a' title='Ver lista' color="primary" sm='sm' icon='search'
                                href="{{ route('list-config.show', $activo['id']) }}" />
                            <x-btn-standar type='a' title='Editar lista' color="warning" sm='sm'
                                icon='edit' href="{{ route('list-config.edit', $activo['id']) }}" />

                            {!! Form::open(['method' => 'DELETE', 'route' => ['mail-config.destroy', $activo['id']], 'style' => 'display:inline']) !!}
                            <x-btn-standar type='submit' title='Eliminar lista' color="danger" sm='sm'
                                icon='trash' href="{{ route('list-config.edit', $activo['id']) }}" />

                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {!! $data['render']() !!} --}}
    </x-cards>


@endsection
