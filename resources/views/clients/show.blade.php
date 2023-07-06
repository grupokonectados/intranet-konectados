@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('others-btn')
    @can('root-edit')
        <x-btn-standar type='a' name='Editar cliente' title='Editar cliente' extraclass='ml-auto' color="warning" sm='sm'
            icon='edit' href="{{ route('clients.edit', $client->id) }}" />
    @endcan
    <x-btn-standar type='a' name='Diseñar estrategia' title='Diseñar estrategia' color="success" sm='sm' icon='plus-circle'
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
                <th width='10%' class="align-middle text-uppercase text-white text-center">Canal</th>
                <th class="align-middle text-uppercase text-white" width='3%'>Cobertura</th>
                <th class="align-middle text-uppercase text-white" width='3%'>Registros</th>
                <th class="align-middle text-white text-center text-uppercase" width='7%'>¿Acepta repetidos?</th>
                <th class="align-middle text-uppercase text-white" width='3%'>Repetidos</th>
                <th class="align-middle text-uppercase text-white text-center">Criterio</th>
                <th width='5%' class="align-middle text-uppercase text-white text-center">Estado</th>
                <th width='15%' class="align-middle text-white text-center text-uppercase">Fecha activación</th>
                <th width='7%' class="align-middle text-white text-center text-uppercase">Hora activación</th>
                <th class="align-middle text-uppercase text-white text-center">Avance</th>
                <th width='5%' class="align-middle text-uppercase text-white text-center">Acciones</th>
            </thead>
            <tbody>
                @foreach ($datas as $k => $data)
                @if ($data->isActive === 1 && $data->type === 2)
                <tr>
                    <td class="text-center align-middle">
                       {{ $data->canal }}
                    </td>
                    <td class="text-center align-middle">
                        {{ number_format($data->cobertura, 2, ',', '.') }}%
                        
                    </td>
                    <td class="text-center align-middle">
                        {{ number_format($data->registros_unicos, 0, ',', '.') }}
                        
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
                        {{ number_format($data->registros_repetidos, 0, ',', '.') }}
                        
                        
                    </td>
                    <td>{{ $data->onlyWhere }}</td>
                    <td class="text-center align-middle">
                        Activo
                    </td>
                    <td class="text-center align-middle">
                        {{$data->activation_date === null ? 'Sin Activar' : date('d-m-Y', strtotime($data->activation_date)) }}
                    </td>
                    <td class="text-center align-middle">
                        {{ $data->activation_time === null ? 'Sin Activar' : date('G:i:m', strtotime($data->activation_time)) }}
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
                @endif
            @endforeach
            </tbody>
            @if (isset($suma_total))
                <tfoot>
                    <tr>
                        <td></td>
                        <th class="text-center">{{ number_format($porcentaje_total, 2, ',', '.') }}%</th>
                        <th class="text-center">{{ number_format($suma_total, 0, ',', '.') }}</th>
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
            @endif
            
        </table>
    </x-cards>

    <x-cards header="Estrategias historico" titlecolor='danger' xtrasclass="my-3">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-dark">
                <th width='10%' class="align-middle text-uppercase text-white text-center">Canal</th>
                <th class="align-middle text-uppercase text-white text-center">Criterio</th>
                <th width='15%' class="align-middle text-white text-center text-uppercase">Fecha activación</th>
                <th width='7%' class="align-middle text-white text-center text-uppercase">Hora activación</th>
                <th class="align-middle text-uppercase text-white text-center">Avance</th>
            </thead>
            <tbody>
                @foreach ($datas as $k => $data)
                @if ($data->isActive === 0 && $data->isDelete === 1 && $data->type === 2)
                <tr>
                    <td class="text-center align-middle">
                       {{ $data->canal }}
                    </td>
                    
                    <td>{{ $data->onlyWhere }}</td>
                    
                    <td class="text-center align-middle">
                        {{$data->activation_date === null ? 'Sin Activar' : date('d-m-Y', strtotime($data->activation_date)) }}
                    </td>
                    <td class="text-center align-middle">
                        {{ $data->activation_time === null ? 'Sin Activar' : date('G:i:m', strtotime($data->activation_time)) }}
                    </td>
                    <td class="text-center align-middle ">
                        <div class="progress" role="progressbar" aria-label="Animated striped example"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progres"
                                style="width: 75%"><span id="texto_progress"></span></div>
                        </div>
                    </td>

                </tr>
                @endif
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
