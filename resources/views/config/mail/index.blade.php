@extends('layouts.app')
@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-create')
    <x-btn-standar type='a' name='Nuevo configuracion' title='Nuevo configuracion' color="success" sm='sm'
        icon='plus-circle' href="{{ route($config_layout['btn-create'], 'prefix='.$request->prefix) }}" />
        
@endsection

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], $id_cliente) }}" />
@endsection


@section('content')

    <x-cards  header="Templates Activos" titlecolor='primary'>
        <table class="table table-bordered table-sm mb-0">
            <thead class="text-center align-middle bg-dark text-uppercase">
                <tr>
                    <th class="text-white" scope="col">Nombre</th>
                    <th class="text-white" scope="col" width="280px">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($data as $key => $activo)
                    <tr class="text-uppercase">
                        <td>{{ $activo['nombreTemplate'] }}</td>
                        <td>
                            <x-btn-standar type='a' title='Ver Usuario' color="primary" sm='sm' icon='search'
                                href="{{ route('mail-config.show', $activo['id']) }}" />
                            <x-btn-standar type='a' title='Editar usuario' color="warning" sm='sm'
                                icon='edit' href="{{ route('mail-config.edit', $activo['id']) }}" />

                            {!! Form::open(['method' => 'DELETE', 'route' => ['mail-config.destroy', $activo['id']], 'style' => 'display:inline']) !!}
                            <x-btn-standar type='submit' title='Eliminar usuario' color="danger" sm='sm'
                                icon='trash' href="{{ route('mail-config.edit', $activo['id']) }}" />

                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {!! $data['render']() !!} --}}
    </x-cards>


@endsection
