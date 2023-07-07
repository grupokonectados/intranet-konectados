@extends('layouts.app')
@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-create')
    <x-btn-standar type='a' name='Nuevo Usuario' title='Nuevo Usuario' color="success" sm='sm' icon='plus-circle'
        href="{{ route($config_layout['btn-create']) }}" />
@endsection

@section('content')

    <x-cards header="Usuarios" titlecolor='primary'>
        <table class="table table-bordered table-sm mb-0">
            <thead class="text-center align-middle bg-dark text-uppercase">
                <tr>
                    <th class="text-white" scope="col">Nombre</th>
                    <th class="text-white" scope="col">Correo</th>
                    <th class="text-white" scope="col">Permisologia</th>
                    <th class="text-white" scope="col" width="280px">Acciones</th>
                </tr>

            </thead>
            <tbody class="text-center">
                @foreach ($data as $key => $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if (!empty($user->getRoleNames()))
                                @foreach ($user->getRoleNames() as $v)
                                    <span class="badge text-bg-primary">{{ $v }}</span>
                                @endforeach
                            @endif
                        </td>
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

        {!! $data->render() !!}
    </x-cards>

@endsection
