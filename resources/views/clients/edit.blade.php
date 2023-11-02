@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], $client->id) }}" />
@endsection


@section('content')
    <x-cards size='6' header="Configuracion de canales" titlecolor='primary'>
        {!! Form::model($client, ['method' => 'PATCH', 'route' => ['clients.update', $client->id]]) !!}
        <div class="row mb-2">
            <table class="table table-sm table-bordered ">
                <thead class="table-dark text-uppercase align-middle">
                    <tr>
                        <th width='50%'>Canales:</th>
                        <th>tipo de canal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($channels as $k => $value)
                        <tr>
                            <td>
                                {{ Form::checkbox('configuracion[channels][' . $k . '][seleccionado]', $k, isset($channels_config['channels'][$k]) ? true : false, ['id' => 'checkbox_' . $k, 'class' => 'name form-check-input', 'onchange' => 'enableRadio(this, ' . $k . ')']) }}
                                <label for="checkbox_{{ $k }}"
                                    class="form-check-label">{{ strtoupper($value['name']) }}</label>
                            </td>
                            <td>
                                {{ Form::checkbox(
                                    'configuracion[channels][' . $k . '][tipo]',
                                    $k . '_1',
                                    isset($channels_config['channels'][$k]['tipo']) ? true : false,
                                    [
                                        'class' => 'name form-check-input',
                                        'id' => 'check_tipo_' . $k,
                                        'disabled' => isset($channels_config['channels'][$k]['seleccionado']) ? false : true,
                                    ],
                                ) }}
                                <label class="form-check-label">Masivo</label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-cards>

    <x-cards size='6' header="Configuracion de estructura" titlecolor='primary'>
        <table class="table table-bordered table-sm mb-0">

            <thead class="table-dark text-uppercase text-center">
                <tr>
                    <th>Campo BD</th>
                    <th>Nombre</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                @foreach ($estructura as $ke => $estruc)
                    <tr>
                        <td>
                            {{ $estruc['COLUMN_NAME'] }}
                        </td>
                        <td>
                            @if (isset($channels_config['estructura']))
                                @if (in_array($estruc['COLUMN_NAME'], array_keys($channels_config['estructura'])))
                                    @if (isset($channels_config['estructura'][$estruc['COLUMN_NAME']]['utilizar']))
                                        <input class="form-control form-control-sm" placeholder="Ingrese un nombre"
                                            type="text"
                                            name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][nombre]"
                                            value="{{ $channels_config['estructura'][$estruc['COLUMN_NAME']]['nombre'] }}" />
                                    @else
                                        <input type="text" placeholder="Ingrese un nombre"
                                            class="form-control form-control-sm"
                                            name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][nombre]" />
                                    @endif
                                @endif
                            @else
                                <input class="form-control form-control-sm" placeholder="Ingrese un nombre" type="text"
                                    name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][nombre]" />
                            @endif
                            {{-- 
                        <input type="text"
                                placeholder="Ingrese un nombre"
                                class="form-control form-control-sm"
                                    name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][nombre]" /> --}}
                        </td>

                        <td class="text-center">
                            @if (isset($channels_config['estructura']))
                                @if (in_array($estruc['COLUMN_NAME'], array_keys($channels_config['estructura'])))
                                    @if (isset($channels_config['estructura'][$estruc['COLUMN_NAME']]['utilizar']))
                                        <input type="checkbox"
                                            name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][utilizar]"
                                            checked
                                            value="{{ $channels_config['estructura'][$estruc['COLUMN_NAME']]['utilizar'] }}" />
                                    @else
                                        <input type="checkbox"
                                            name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][utilizar]" />
                                    @endif
                                @endif
                            @else
                                <input type="checkbox"
                                    name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][utilizar]" />
                            @endif
                            {{-- <input type="checkbox"
                                    name="configuracion[estructura][{{ $estruc['COLUMN_NAME'] }}][utilizar]" /> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </x-cards>

    <div class="row">
        <div class="col-5 align-self-center pr-0">
            <x-btn-standar type='submit' name='Actualizar' title='Actualizar' color="success" sm='sm'
                icon='sync-alt' />
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('js')
    <script>
        function enableRadio(element, c) {
            var check_tipo = document.getElementById("check_tipo_" + c)
            if (check_tipo.hasAttribute("disabled")) {
                check_tipo.removeAttribute("disabled")
            } else {
                check_tipo.setAttribute("disabled", true);
            }
        }
    </script>
@endsection
