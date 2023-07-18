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
                        <th>Se puede repetir el canal en la estrategia?:</th>
                        <th>tipo de canal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($channels as $k => $value)
                        <tr>
                            <td>
                                {{ Form::checkbox('channels[' . $k . '][seleccionado]', $k, isset($channels_config[$k]) ? true : false, ['id' => 'checkbox_'.$k, 'class' => 'name form-check-input', 'onchange' => 'enableRadio(this, ' . $k . ')']) }}
                                <label for="checkbox_{{ $k }}" class="form-check-label">{{ $value['name'] }}</label>
                            </td>
                            <td>
                                {{ Form::checkbox('channels[' . $k . '][multiple]', $k.'_1', isset($channels_config[$k]['multiple']) ? true : false, ['class' => 'name form-check-input', 'id' => 'check_' . $k, 'disabled' => isset($channels_config[$k]['seleccionado']) ? false : true]) }}
                                <label class="form-check-label">Si</label>
                            </td>
                            <td>
                                {{ Form::checkbox('channels[' . $k . '][tipo]', $k.'_1', isset($channels_config[$k]['tipo']) ? true : false, ['class' => 'name form-check-input', 'id' => 'check_tipo_' . $k, 'disabled' => isset($channels_config[$k]['seleccionado']) ? false : true]) }}
                                <label class="form-check-label">Masivo</label>
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
            var check_multiple = document.getElementById("check_" + c)
            var check_tipo = document.getElementById("check_tipo_" + c)
            if (check_multiple.hasAttribute("disabled")) {
                check_multiple.removeAttribute("disabled")
                check_tipo.removeAttribute("disabled")
            } else {
                check_multiple.setAttribute("disabled", true);
                check_tipo.setAttribute("disabled", true);
            }
        }
    </script>
@endsection
