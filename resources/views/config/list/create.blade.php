@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back']) }}" />
@endsection


@section('content')

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

    <x-cards header="Nueva lista" titlecolor='success'>
        {!! Form::open(['route' => 'list-config.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
        <div class="row">
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Cliente:</strong>
                    {!! Form::text('no', $data['prefix'], ['disabled', 'class' => 'form-control form-control-sm']) !!}
                    <input type="hidden" name="prefix" id="prefix" value="{{ $data['prefix'] }}">
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Nombre:</strong>
                    {!! Form::text('nombre', null, ['placeholder' => 'Nombre de la lista', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Discador</strong>
                    {!! 
                    Form::select('discador', ['1' => 'URL 1', '2' => 'URL 2'], null, ['placeholder' => 'Seleccione', 'class' => 'form-select form-select-sm']); !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Lista</strong>
                    {!! 
                    Form::select('discador', ['1' => 'Lista 1', '2' => 'Lista 2'], null, ['placeholder' => 'Seleccione', 'class' => 'form-select form-select-sm']); !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>IVR:</strong>
                    
                    <div class="form-check">
                        {{ Form::checkbox('IVR', '', false, array('class' => 'form-check-input')) }}
                        <label class="form-check-label" for="flexCheckChecked">
                            IVR
                        </label>
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
