@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('others-btn')
    <x-btn-standar 
        type='a' 
        name='Editar cliente' 
        title='Editar cliente' 
        extraclass='ml-auto' 
        color="warning" 
        sm='sm'
        icon='edit' 
        href="{{ route('clients.edit', $client->id) }}" />

    <x-btn-standar 
        type='a' 
        name='Diseñar estrategia' 
        title='Diseñar estrategia' 
        color="success" 
        sm='sm' 
        icon='plus-circle'
        href="{{ route('clients.diseno', $client->id) }}" />

@endsection

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back']) }}" />
@endsection


@section('content')

    <x-cards header="Estrategias activas" titlecolor='success'>
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-dark">
                <th width='10%' class="align-middle text-uppercase text-white text-center">
                    
                    <div class="d-flex justify-content-around">
                        <div class="align-self-center">
                            Canal 
                        </div>
                        <div class="d-flex flex-column">
                            <a href="/clients/{{ $client->id }}?channelorder=ASC" class="text-white">
                                <i class="fas fa-chevron-up fa-xs"></i>
                            </a>
                            <a href="/clients/{{ $client->id }}?channelorder=DESC" class="text-white">
                                <i class="fas fa-chevron-down fa-xs"></i>
                            </a>
                            
                           
                        </div>
                        
                    </div>
                    
                
                
                </th>
                <th class="align-middle text-uppercase text-white" width='3%'>Cobertura</th>
                <th class="align-middle text-uppercase text-white" width='3%'>Registros</th>
                <th class="align-middle text-white text-center text-uppercase" width='7%'>¿Acepta repetidos?</th>
                <th class="align-middle text-uppercase text-white" width='3%'>Repetidos</th>
                <th class="align-middle text-uppercase text-white text-center">Criterio</th>
                <th width='5%' class="align-middle text-uppercase text-white text-center">Estado</th>
                <th width='15%' class="align-middle text-white text-center text-uppercase" >
                    <div class="d-flex justify-content-around">
                    <div class="align-self-center">
                        Fecha activación 
                    </div>
                    <div class="d-flex flex-column">
                        <a href="/clients/{{ $client->id }}?dateorder=ASC" class="text-white">
                            <i class="fas fa-chevron-up fa-xs"></i>
                        </a>
                        <a href="/clients/{{ $client->id }}?dateorder=DESC" class="text-white">
                            <i class="fas fa-chevron-down fa-xs"></i>
                        </a>
                        
                       
                    </div>
                </th>
                <th width='7%' class="align-middle text-white text-center text-uppercase" >Hora activación</th>
                <th class="align-middle text-uppercase text-white text-center">Avance</th>
                <th width='5%' class="align-middle text-uppercase text-white text-center">Acciones</th>
            </thead>
            <tbody>
                @foreach ($dataEstrategias as $k => $data)
                    <tr>
                        <td class="text-center align-middle">
                            {{ $dataChart[$k]['title'] }}
                        </td>
                        <td class="text-center align-middle">
                            {{ $dataChart[$k]['porcentaje'] }}%
                        </td>
                        <td class="text-center align-middle">
                            {{ $dataChart[$k]['datos'] }}
                        </td>
                        <td class="align-middle">
                            @if ($data->repeatUsers == 1)
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">Si</label>
                                    <input class="form-check-input" disabled checked id="check" type="checkbox">
                                </div>
                            @else
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">No</label>
                                    <input class="form-check-input" disabled id="check" type="checkbox">
                                </div>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($data->repeatUsers == 1)
                                {{ $total_resta }}
                            @else
                                0
                            @endif
                        </td>
                        <td>{{ $data->onlyWhere }}</td>
                        <td class="text-center align-middle" >
                            Activo
                        </td>
                        <td class="text-center align-middle">
                            {{ date('d-m-Y', strtotime($data->activation_date)) }} 
                        </td>
                        <td class="text-center align-middle">
                            {{ date('G:i:m', strtotime($data->activation_time)) }}
                        </td>
                        <td class="text-center align-middle ">
                            <div class="progress" role="progressbar" aria-label="Animated striped example"
                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="progres"
                                    style="width: 75%"><span id="texto_progress"></span></div>
                            </div>
                        </td>

                        <td class="text-center align-middle">
                            <x-btn-standar type='a' title='Detener' extraclass='detener-estrategia'
                                href="{{ route('estrategia.stop-strategy', $data->id) }}" color="danger" sm='sm'
                                icon='stop-circle' />

                        </td>
                    </tr>
                @endforeach
                @if ($cuenta_total['porcentual'] !== 0)
                <tr>
                    <td></td>
                    <td class="text-center align-middle">{{ $cuenta_total['porcentual'] }}%</td>
                    <td class="text-center align-middle">{{ $cuenta_total['total'] }}</td>
                    <td colspan="8"></td>

                </tr>
                @endif
                
            </tbody>
        </table>
    </x-cards>

    <x-cards header="Estrategias historico" titlecolor='danger' xtrasclass="my-3">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-dark ">
                <th class="align-middle text-white text-center text-uppercase" ><div class="d-flex justify-content-around">
                    <div class="align-self-center">
                        Canal 
                    </div>
                    <div class="d-flex flex-column">
                        <a href="/clients/{{ $client->id }}?channelnotorder=ASC" class="text-white">
                            <i class="fas fa-chevron-up fa-xs"></i>
                        </a>
                        <a href="/clients/{{ $client->id }}?channelnotorder=DESC" class="text-white">
                            <i class="fas fa-chevron-down fa-xs"></i>
                        </a>
                        
                       
                    </div>
                    
                </div></th>
                <th class="align-middle text-white text-center text-uppercase" >Criterio</th>
                <th class="align-middle text-white text-center text-uppercase" >
                    <div class="d-flex justify-content-around">
                    <div class="align-self-center">
                        Fecha activación 
                    </div>
                    <div class="d-flex flex-column">
                        <a href="/clients/{{ $client->id }}?datenotorder=ASC" class="text-white">
                            <i class="fas fa-chevron-up fa-xs"></i>
                        </a>
                        <a href="/clients/{{ $client->id }}?datenotorder=DESC" class="text-white">
                            <i class="fas fa-chevron-down fa-xs"></i>
                        </a>
                        
                       
                    </div>
                </th>
                <th class="align-middle text-white text-center text-uppercase" >Hora activación</th>
                <th class="align-middle text-white text-center text-uppercase" >Avance</th>
            </thead>
            <tbody>
                @foreach ($dataEstrategiasNot as $k => $data)
                    <tr>
                        <td class="align-middle text-center">
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

                        <td class="align-middle">{{ $data->onlyWhere }}</td>
                        <td class="align-middle text-center">
                            {{ date('d-m-Y', strtotime($data->activation_date)) }}
                        </td>
                        <td class="align-middle text-center">
                            {{ date('G:i:m', strtotime($data->activation_time)) }}
                        </td>
                        <td class="align-middle text-center">
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
    </x-cards>

@endsection
@section('js')

<script>

    const enlacesElement = document.querySelectorAll('.detener-estrategia');


        if (enlacesElement !== null) {
            enlacesElement.forEach((enlaceElement) => {
                enlaceElement.addEventListener('click', (event) => {
                    const confirmacion = confirm('¿Desea detener la estrategia?');
                    if (!confirmacion) {
                        event.preventDefault();
                    }
                });
            });
        }
</script>
    
@endsection