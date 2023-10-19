@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], 'prefix=' . $data['prefix']) }}" />
@endsection



@section('content')
    <x-cards titlecolor='primary'>
        <div class="row">
            <div class="col-4">
                <label class="text-uppercase">Cliente: </label>
                <label class="h6" for="">{!! $data['prefix'] !!}</label>
            </div>
            <div class="col-4">
                <label class="text-uppercase" for="">Nombre: </label>
                <label class="h6" for="">{!! $data['nombreTemplate'] !!}</label>
            </div>
            <div class="col-6">
                <label class="text-uppercase" for="">Asunto del email: </label>
                <label class="h6" for="">{!! $data['asunto'] !!}</label>
            </div>
            <div class="col-6">
                <label class="text-uppercase" for="">Columnas: </label>
                <label class="h6" for="">
                    @foreach ($data['columnas'] as $key => $columna)
                        {!! $columna !!}
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </label>
            </div>
            <div class="col-12">
                <label class="text-uppercase" for="">Email del cliente que envia: </label>
                <label class="h6" for="">{!! $data['emailFrom'] !!}</label>
            </div>
            <div class="col-12">
                <label class="text-uppercase" for="">Nombre del cliente que envia: </label>
                <label class="h6" for="">{!! $data['nombreFrom'] !!}</label>
            </div>
            <div class="col-12">
                <label class="text-uppercase" for="">Email de respuesta: </label>
                <label class="h6" for="">{!! $data['emailReply'] !!}</label>
            </div>
        </div>






    </x-cards>


@endsection
