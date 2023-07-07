@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back']) }}" />
@endsection

@section('others-btn')
    <x-btn-standar type='a' name='Editar' title='Editar usuario' color="warning" sm='sm' icon='edit'
        href="{{ route($config_layout['btn-edit'], $user->id) }}" />
@endsection



@section('content')

    <x-cards titlecolor='primary'>

        <table class="table table-sm mb-0 table-bordered">
            <tr>
                <td class="text-uppercase bg-dark text-white w-25"><strong>Nombre y Apellido:</strong></td>
                <td><span class="mx-3">{{ $user->name }}</span></td>
            </tr>
            <tr>
                <td class="text-uppercase bg-dark text-white w-25"><strong>Correo Electronico:</strong></td>
                <td><span class="mx-3">{{ $user->email }}</span></td>
            </tr>
            <tr>
                <td class="text-uppercase bg-dark text-white w-25"><strong>Permisos:</strong></td>
                <td>
                    @if (!empty($user->getRoleNames()))
                        @foreach ($user->getRoleNames() as $v)
                            <span class="badge text-bg-primary mx-3">{{ $v }}</span>
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-uppercase bg-dark text-white w-25"><strong>Clientes:</strong></td>
                <td class="align-middle">
                    @for ($i = 0; $i < count($clients); $i++)
                        @if (in_array($clients[$i]->id, $user->ve_clientes))
                            @if ($i === count($clients) - 1)
                                {{ $clients[$i]->name }}
                            @else
                                {{ $clients[$i]->name }},
                            @endif
                        @endif
                    @endfor
                </td>
            </tr>
        </table>
    </x-cards>


@endsection
