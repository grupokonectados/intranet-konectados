@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], 'prefix=' . $data['prefix']) }}" />
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
        {!! Form::model($data, ['method' => 'PATCH', 'route' => ['mail-config.update', $data['id']]]) !!}
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
                    {!! Form::text('nombreTemplate', null, [
                        'placeholder' => 'Nombre del template',
                        'class' => 'form-control form-control-sm',
                    ]) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Email del cliente que envia:</strong>
                    {!! Form::text('emailFrom', null, [
                        'placeholder' => 'Email del cliente que envia',
                        'class' => 'form-control form-control-sm',
                    ]) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Nombre del cliente que envia:</strong>
                    {!! Form::text('nombreFrom', null, [
                        'placeholder' => 'Nombre del cliente que envia',
                        'class' => 'form-control form-control-sm',
                    ]) !!}
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Asunto del email:</strong>
                    {!! Form::text('asunto', null, ['placeholder' => 'Asunto del email', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Email de respuesta:</strong>
                    {!! Form::text('emailReply', null, [
                        'placeholder' => 'Email de respuesta',
                        'class' => 'form-control form-control-sm',
                    ]) !!}
                </div>
            </div>

            {{-- @dd($data['columnas']) --}}
            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Columnas:</strong>
                    @foreach ($columnas as $columna)
                        <div class="form-check">
                            {{ Form::checkbox('columnas[]', $columna, in_array($columna, $data['columnas']) ? true : false, ['class' => 'form-check-input']) }}
                            <label class="form-check-label" for="flexCheckChecked">
                                {{ $columna }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Otras columnas:</strong>
                    @foreach ($columnas_calculadas as $columna2)
                        <div class="form-check">
                            {{ Form::checkbox('columnas_calculadas[]', $columna2, false, ['class' => 'form-check-input']) }}
                            <label class="form-check-label" for="flexCheckChecked">
                                {{ $columna2 }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>




            <div class="col-6 mb-3">
                <div class="form-group">
                    <strong>Plantilla:</strong>
                    {{ Form::file('template', ['class' => 'form-control form-control-sm']) }}
                </div>
            </div>

            <div class="col-12 mb-0">
                <x-btn-standar type='submit' name='Guardar' color="success" sm='sm' icon='save' />
            </div>
        </div>
        {!! Form::close() !!}

    </x-cards>



@endsection
