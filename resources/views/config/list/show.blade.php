@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    {{-- <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], 'prefix=' . $data['prefix']) }}" /> --}}
@endsection



@section('content')
    <x-cards titlecolor='primary'>
        <div class="row">
            <div class="col-4">
                <label class="text-uppercase">Nombre: </label>
                <label class="h6" for="">{!! $data['nombre'] !!}</label>
            </div>
            <div class="col-4">
                <label class="text-uppercase" for="">Discador: </label>
                <label class="h6" for="">{!! $data['discador'] !!}</label>
            </div>
            <div class="col-6">
                <label class="text-uppercase" for="">IVR: </label>
                <label class="h6" for="">@if($data['IVR'] === true)Si @else no @endif</label>
            </div>
        </div>




    </x-cards>


@endsection
