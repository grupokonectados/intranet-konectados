@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('others-btn')
    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm ml-auto">
        <i class="fas fa-edit"></i>
        Editar cliente
    </a>
    <a href="{{ route('clients.diseno', $client->id) }}" class="btn btn-success btn-sm ml-auto">
        <i class="fas fa-plus-circle"></i>
        Diseñar estrategia
    </a>
@endsection
@section('btn-back')
    <a href="{{ route($config_layout['btn-back']) }}" class="btn btn-dark btn-sm">
        <i class="fas fa-chevron-circle-left"></i>
        Regresar
    </a>
@endsection


@section('content')
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estrategias activas</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <th class="align-middle text-center">Canal</th>
                        <th class="align-middle" width='3%'>Cobertura</th>
                        <th class="align-middle" width='3%'>Registros</th>
                        <th width='7%'>¿Acepta repetidos?</th>
                        <th class="align-middle" width='3%'>Repetidos</th>
                        <th class="align-middle text-center">Criterio</th>
                        <th class="align-middle text-center">Estado</th>
                        <th class="align-middle text-center">Fecha</th>
                        <th class="align-middle text-center">Avance</th>
                        <th class="align-middle text-center">Acciones</th>
                    </thead>
                    <tbody>
                        @foreach ($dataEstrategias as $k => $data)
                            <tr>
                                <td class="text-center">
                                    {{ $dataChart[$k]['title'] }}
                                </td>
                                <td class="text-center">
                                    {{ $dataChart[$k]['porcentaje'] }}%
                                </td>
                                <td class="text-center">
                                    {{ $dataChart[$k]['datos'] }}
                                </td>
                                <td>
                                    @if ($data->repeatUsers == 1)
                                        <div class="form-check form-switch align-items-stretch">
                                            <label for="form-check-label">Si</label>
                                            <input class="form-check-input ml-0" disabled checked id="check"
                                                type="checkbox">
                                        </div>
                                    @else
                                        <div class="form-check form-switch align-items-stretch">
                                            <label for="form-check-label">No</label>
                                            <input class="form-check-input ml-0" disabled id="check" type="checkbox">
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data->repeatUsers == 1)
                                        {{ $total_resta }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>{{ $data->onlyWhere }}</td>
                                <td>
                                    Activo
                                </td>
                                <td class="text-center">
                                    {{ date('d-m-Y', strtotime($data->activation_date)) }} /
                                    {{ date('G:i:m', strtotime($data->activation_time)) }}
                                </td>
                                <td>
                                    <div class="progress" role="progressbar" aria-label="Animated striped example"
                                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progres"
                                            style="width: 75%"><span id="texto_progress"></span></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a title="Detener" href="{{ route('estrategia.stop-strategy', $data->id) }}"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-stop-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td class="text-center">{{ $cuenta_total['porcentual'] }}%</td>
                            <td class="text-center">{{ $cuenta_total['total'] }}</td>
                            <td colspan="6"></td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 my-3">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">Estrategias historico</h5>
            </div>
            <div class="card-body ">

                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <th>Canal</th>
                        <th>Criterio</th>
                        <th>Fecha</th>
                        <th>Avance</th>
                    </thead>
                    <tbody>
                        @foreach ($dataEstrategiasNot as $k => $data)
                            <tr>
                                <td>
                                    @switch($data->channels)
                                        @case(1)
                                            AGENTE
                                        @break

                                        @case(2)
                                            IVR
                                        @break

                                        @case(3)
                                            VOICE BOT
                                        @break

                                        @case(4)
                                            SMS
                                        @break

                                        @case(5)
                                            EMAIL
                                        @break

                                        @case(6)
                                            WHATSAPP
                                        @break
                                    @endswitch
                                </td>

                                <td>{{ $data->onlyWhere }}</td>
                                <td>
                                    {{ date('d-m-Y', strtotime($data->activation_date)) }} /
                                    {{ date('G:i:m', strtotime($data->activation_time)) }}
                                </td>
                                <td>
                                    <div class="progress" role="progressbar" aria-label="Animated striped example"
                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progres"
                                            style="width: 100%"><span id="texto_progress"></span></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
