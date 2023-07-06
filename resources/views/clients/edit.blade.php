@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
    href="{{ route($config_layout['btn-back'], $client->id) }}" />
@endsection


@section('content')
    <x-cards header="Editar cliente" titlecolor='warning'>

        {!! Form::model($client, ['method' => 'PATCH', 'route' => ['clients.update', $client->id]]) !!}
        <div class="row mb-3">
            <div class="col-2 align-self-center pr-0">
                <label class="form-label mb-0">Nombre:</label>
            </div>
            <div class="col-4 pl-0">
                <input type="text" value="{{ $client->name }}" disabled class="form-control">

            </div>

            <div class="col-2 align-self-center pr-0">
                <label class="form-label mb-0">Prefijos:</label>
            </div>
            <div class="col-4 pl-0">
                <input type="text" value="{{ $client->prefix }}" disabled class="form-control">
            </div>
        </div>

        <div class="row mb-2">

            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th width='50%'>Canales:</th>
                        <th colspan="2">Se puede repetir el canal en la estrategia?:</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($channels as $k => $value)
                        <tr>
                            <td>
                                {{ Form::checkbox('channels[' . $k . '][seleccionado]', $k, isset($client->active_channels[$k]) ? true : false, ['id' => 'checkbox_'.$k, 'class' => 'name form-check-input', 'onchange' => 'enableRadio(this, ' . $k . ')']) }}
                                <label for="checkbox_{{ $k }}" class="form-check-label">{{ $value }}</label>
                            </td>
                            <td>
                                {{ Form::checkbox('channels[' . $k . '][multiple]', $k.'_1', isset($client->active_channels[$k]['multiple']) ? true : false, ['class' => 'name form-check-input', 'id' => 'check_' . $k, 'disabled' => isset($client->active_channels[$k]) ? false : true]) }}
                                <label class="form-check-label">Si</label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-5 align-self-center pr-0">
                <x-btn-standar type='submit' name='Actualizar' title='Actualizar' color="success" sm='sm' icon='sync-alt' />
            </div>
        </div>


        {!! Form::close() !!}
    </x-cards>
@endsection

@section('js')
    <script>
        function enableRadio(element, c) {
            var radio = document.getElementById("check_" + c)
            if (radio.hasAttribute("disabled")) {
                radio.removeAttribute("disabled")
            } else {
                radio.setAttribute("disabled", true);
            }
        }
    </script>
@endsection
