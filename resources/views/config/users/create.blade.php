@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back']) }}" />
@endsection


@section('content')

{{-- @dd($clients) --}}

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong>Ha ocurrido un error.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-cards header="Nuevo usuario" titlecolor='success'>
        {!! Form::open(['route' => 'users.store', 'method' => 'POST']) !!}
        <div class="row">
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Nombre y Apellido:</strong>
                    {!! Form::text('name', null, ['placeholder' => 'Nombre y Apellido', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Correo Electronico:</strong>
                    {!! Form::text('email', null, ['placeholder' => 'Correo Electronico', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Contraseña:</strong>
                    {!! Form::password('password', ['placeholder' => 'Contraseña', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">

                
                <div class="form-group">
                    <strong>Confirma la Contraseña:</strong>
                    {!! Form::password('confirm-password', ['placeholder' => 'Confirma la Contraseña', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">

                
                <div class="form-group">
                    <span class=""><strong>Permisos:</strong></span>
                    {!! Form::select('roles[]', $roles, [], ['class' => 'form-select multiple form-select-sm', 'multiple']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <strong>Clientes:</strong>
                <div class="border p-2">
                    <div class="row">
                        @foreach ($data as $key => $value)
                            <div class="col-4">
                                {{ Form::checkbox('ve_clientes[]', $key, '', ['class' => 'form-check-input']) }}
                                <label class="form-check-label">{{ $value }}</label>
                            </div>
                            
                            @endforeach
                    </div>
                </div>
            </div>
            <div class="col-12 mb-0">
                <x-btn-standar type='submit' name='Guardar' color="success" sm='sm' icon='save' />
            </div>
        </div>
        {!! Form::close() !!}

    </x-cards>



@endsection
