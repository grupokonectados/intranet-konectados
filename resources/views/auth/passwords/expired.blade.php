@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('content')


<x-cards size='6' header="Actualizar constraseña" titlecolor='danger'>
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    <a href="/">Ir al inicio</a>
@else
<div class="alert alert-danger">
    Su contraseña a expirado, por favor actualizala.
</div>
<form class="form-horizontal" method="POST" action="{{ route('password.post_expired') }}">

    {{ csrf_field() }}
<div class="col-12 mb-3">
    <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
        <label for="current_password" class="col-md-4 control-label">Contraseña actual</label>

        
            <input id="current_password" type="password" class="form-control" name="current_password" required="">

            @if ($errors->has('current_password'))
                <span class="help-block">
                    <strong>{{ $errors->first('current_password') }}</strong>
                </span>
            @endif
        
    </div>
</div>
    
<div class="col-12 mb-3">
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <label for="password" class="col-md-4 control-label">Nueva constraseña</label>

        
            <input id="password" type="password" class="form-control" name="password" required="">

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        
    </div></div>
<div class="col-12 mb-3">
    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
        <label for="password-confirm" class="col-md-4 control-label">Confirme su nueva contraseña</label>
        
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required="">

            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
       
    </div></div>

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <x-btn-standar type='submit' name='Actualizar' color="success" sm='sm' icon='sync' />
        </div>
    </div>
</form>
@endif

</x-cards>



@endsection