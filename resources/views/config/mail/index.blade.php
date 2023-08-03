@extends('layouts.app')
@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-create')
    <x-btn-standar type='a' name='Nuevo configuracion' title='Nuevo configuracion' color="success" sm='sm'
        icon='plus-circle' href="{{ route($config_layout['btn-create']) }}" />
@endsection

@section('content')

    <x-cards header="Usuarios" titlecolor='primary'>
        <table class="table table-bordered table-sm mb-0">
            <thead class="text-center align-middle bg-dark text-uppercase">
                <tr>
                    <th class="text-white" scope="col">Nombre</th>
                    <th class="text-white" scope="col">Cliente</th>
                    <th class="text-white" scope="col">URL Plantilla</th>
                    <th class="text-white" scope="col">Tipo</th>
                    <th class="text-white" scope="col">Estado</th>
                    <th class="text-white" scope="col" width="280px">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($data as $key => $user)
                    <tr class="text-uppercase">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->prefix }}</td>
                        <td>{{ $user->template_uri }}</td>
                        
                        <td>
                            @switch($user->type)
                                @case(0)
                                Castigo
                                @break

                                @case(1)
                                Infractor
                                @break

                                @case(2)
                                Peaje
                                @break

                                @case(3)
                                Vencido
                                @break
                            @endswitch
                        </td>
                        <td>{{ $user->isActive === 1 ? 'Activo' : 'Inactivo' }}</td>

                        <td>
                            <x-btn-standar type='a' title='Ver Usuario' color="primary" sm='sm' icon='search'
                                href="{{ route('users.show', $user->id) }}" />
                            <x-btn-standar type='a' title='Editar usuario' color="warning" sm='sm'
                                icon='edit' href="{{ route('users.edit', $user->id) }}" />

                            {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => 'display:inline']) !!}
                            <x-btn-standar type='submit' title='Eliminar usuario' color="danger" sm='sm'
                                icon='trash' href="{{ route('users.edit', $user->id) }}" />

                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {!! $data->render() !!} --}}
    </x-cards>

@endsection
