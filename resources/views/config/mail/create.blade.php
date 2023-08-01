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
            <strong>Whoops!</strong>Something went wrong.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-cards header="Nueva configuracion" titlecolor='success'>
        {!! Form::open(['route' => 'mail-config.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
        <div class="row">
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Descripcion:</strong>
                    {!! Form::text('name', null, ['placeholder' => 'Descripcion', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Cliente:</strong>
                    {!! Form::select('prefix', $data, null, ['placeholder' => 'Seleccione', 'class' => 'form-select form-select-sm']) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>emailfrom:</strong>
                    {!! Form::text('emailfrom', null, ['placeholder' => 'Nombre', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>nombrefrom:</strong>
                    {!! Form::text('nombrefrom', null, ['placeholder' => 'Nombre', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>asunto:</strong>
                    {!! Form::text('asunto', null, ['placeholder' => 'Nombre', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>


            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>emailReply:</strong>
                    {!! Form::text('emailReply', null, ['placeholder' => 'Nombre', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Tipo:</strong>
                    {!! Form::select('type', ['0' => 'castigo', '1' => 'infractor', '2' => 'peaje', '3' => 'vencido'], null, ['placeholder' => 'Seleccione', 'class' => 'form-select form-select-sm']) !!}
                </div>
            </div>


            
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Plantilla:</strong>
                    <input 
                    type="file" 
                    name="template" 
                    id="inputFile"
                    class="form-control form-control-sm ">
                </div>
            </div>
            
           

            {{-- <div class="col-6 mb-3">               
                <div class="form-group">
                    <strong>Estado:</strong>
                    {!! Form::select('isActive', ['0' => 'Inactiva', '1' => 'Activa'], null, ['placeholder' => 'Seleccione', 'class' => 'form-select form-select-sm']) !!}
                </div>
            </div> --}}


            
            
            <div class="col-12 mb-0">
                <x-btn-standar type='submit' name='Guardar' color="success" sm='sm' icon='save' />
            </div>
        </div>
        {!! Form::close() !!}

    </x-cards>



@endsection
