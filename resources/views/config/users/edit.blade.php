@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back']) }}" />
@endsection


@section('content')
    {{-- @dd($ve_clientes) --}}
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Something went wrong.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-cards header="Nuevo usuario" titlecolor='warning'>
        {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id]]) !!}
        <div class="row">
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Name:</strong>
                    {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Email:</strong>
                    {!! Form::text('email', null, ['placeholder' => 'Email', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Password:</strong>
                    {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Confirm Password:</strong>
                    {!! Form::password('confirm-password', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Role:</strong>
                    {!! Form::select('roles[]', $roles, $userRole, ['class' => 'form-control', 'multiple']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <strong>Clientes:</strong>
                <div class="border p-2">
                    <div class="row">
                        @for ($i = 0; $i < count($clients); $i++)
                            @if (in_array($clients[$i]->id, $user->ve_clientes))
                                <div class="col-4">

                                    {{ Form::checkbox('ve_clientes[]', $clients[$i]->id, true, ['class' => 'form-check-input']) }}
                                    <label class="form-check-label">{{ $clients[$i]->name }}</label>
                                </div>
                            @else
                                <div class="col-4">

                                    {{ Form::checkbox('ve_clientes[]', $clients[$i]->id, false, ['class' => 'form-check-input']) }}
                                    <label class="form-check-label">{{ $clients[$i]->name }}</label>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
            <div class="col-12 mb-0">
                <x-btn-standar type='submit' name='Actualizar' color="success" sm='sm' icon='sync' />
            </div>
        </div>
        {!! Form::close() !!}

    </x-cards>







@endsection