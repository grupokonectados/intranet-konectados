@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])



@section('others-btn')
    @can('clients-edit')
        <x-btn-standar type='a' name='Editar cliente' title='Editar cliente' extraclass='ml-auto' color="warning" sm='sm'
            icon='edit' href="{{ route('clients.edit', $client->id) }}" />
        <x-btn-standar type='a' name='Configurar mail' title='Configurar mail' extraclass='ml-auto' color="secondary" sm='sm'
            icon='edit' href="{{ route('mail-config.index', 'prefix='.$client->prefix) }}" />
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
        <table class="table table-sm table-bordered mb-0 table-hover">
            <thead class="table-dark text-uppercase text-center">
                <th width='5%' class="align-middle">Canal</th>
                <th class="align-middle" width='3%'>Cobertura</th>
                <th class="align-middle" width='3%'>Registros</th>
                <th class="align-middle" width='3%'>Repetidos</th>
                <th class="align-middle">Criterio</th>
                <th width='10%' class="align-middle">Fecha activación</th>
                <th width='10%' class="align-middle">Hora activación</th>
                <th class="align-middle">Avance</th>
                <th width='5%' class="align-middle">Acciones</th>
            </thead>
            <tbody class="text-center">
                @foreach ($datas as $k => $data)
                    @if ($data['type'] == 2)
                        <tr style="cursor: pointer" onclick="window.location='{{ route('estrategia.avance', $data['id']) }}';" >
                            <td class="align-middle">
                                {{ $data['canal'] }}
                            </td>
                            <td class="align-middle">
                                {{ number_format($data['cobertura'], 2, ',', '.') }}%
                            </td>

                            <td class="align-middle">
                                {{ number_format($data['registros_unicos'], 0, ',', '.') }}
                            </td>
                            <td class="align-middle">
                                {{ number_format($data['registros_repetidos'], 0, ',', '.') }}
                            </td>
                            <td>{{ $data['onlyWhere'] }}</td>
                            <td class="align-middle">
                                {{ $data['activation_date'] === null ? 'Sin Activar' : date('d-m-Y', strtotime($data['activation_date'])) }}
                            </td>
                            <td class="align-middle">
                                {{ $data['activation_time'] === null ? 'Sin Activar' : date('G:i:m', strtotime($data['activation_time'])) }}
                            </td>
                            <td class="align-middle ">
                                <div class="progress" role="progressbar" aria-label="Animated striped example"
                                    aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progres"
                                        style="width: 75%"><span id="texto_progress"></span></div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <x-btn-standar type='a' title='Detener' extraclass='detener-estrategia' color="danger"
                                    sm='sm' icon='stop-circle' dataid="{{ $data['id'] }}" />

                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            @if (isset($suma_total))
                <tfoot class="text-center">
                    <tr>
                        <td></td>
                        <th>{{ number_format($porcentaje_total, 2, ',', '.') }}%</th>
                        <th>{{ number_format($suma_total, 0, ',', '.') }}</th>
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
            @endif

        </table>
    </x-cards>

    <x-cards header="Estrategias historico" titlecolor='danger' xtrasclass="my-3">
        <div class="row">
            <div class="col-4 mb-3">
                <select class="form-select form-select-sm" name="selectFiltro"
                    onchange="filtroCanales(this.value, '{{ $client->prefix }}')" id="selectFiltro">
                    <option value="">Seleccione</option>
                    @for ($i = 0; $i < count($channels); $i++)
                        @if (in_array($i, $ch_approve))
                            <option value="{{ $i }}">{{ strtoupper($channels[$i]['name']) }}</option>
                        @endif
                    @endfor
                </select>
            </div>
            <div class="col-2">
                <x-btn-standar name='Limpiar' color="success" sm='sm' icon='sync'
                    onclick="filtroCanales('refresh', '{{ $client->prefix }}')" />
            </div>
            <div class="col-12">
                <table id='tabla_eliminados' class="table table-sm table-bordered mb-0">
                    <thead class="table-dark text-uppercase text-center">
                        <th width='10%' class="align-middle">Canal</th>
                        <th class="align-middle">Criterio</th>
                        <th width='12%' class="align-middle">Fecha activación</th>
                        <th width='12%' class="align-middle">Hora activación</th>
                        <th width='15%' class="align-middle">Avance</th>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($datas as $k => $data)
                            @if ($data['type'] == 3)
                                <tr>
                                    <td class="align-middle">
                                        {{ $data['canal'] }}
                                    </td>
                                    <td>{{ $data['onlyWhere'] }}</td>
                                    <td class="align-middle">
                                        {{ $data['activation_date'] === null ? 'Sin Activar' : date('d-m-Y', strtotime($data['activation_date'])) }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data['activation_time'] === null ? 'Sin Activar' : date('G:i:m', strtotime($data['activation_time'])) }}
                                    </td>
                                    <td class="align-middle ">
                                        <div class="progress" role="progressbar" aria-label="Animated striped example"
                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                id="progres" style="width: 75%"><span id="texto_progress"></span></div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-cards>
@endsection
@section('js')

    <script>
        const enlacesElement = document.querySelectorAll('.detener-estrategia');
        const csrfToken = "{{ csrf_token() }}";

        if (enlacesElement !== null) {
            enlacesElement.forEach((enlaceElement) => {
                enlaceElement.addEventListener('click', (event) => {
                    const confirmacion = confirm('¿Desea detener la estrategia?');
                    if (!confirmacion) {
                        event.preventDefault();
                    } else {
                        fetch(`http://apiest.konecsys.com:8080/estrategia/detener/${enlaceElement.dataset.identificador}`, {
                                method: 'PUT',
                            })
                            .then(response => {
                                if (response.ok) {
                                    // La respuesta fue exitosa (código de estado HTTP 200-299)
                                    return response
                                        .json(); // Devuelve una promesa que resuelve a un objeto JSON
                                } else {
                                    // La respuesta no fue exitosa
                                    throw new Error('Error de respuesta');
                                }
                            })
                            .then(data => {
                                // Haz algo con los datos recibidos
                                if (data.status === "201") {
                                    alert('Detenido con exito')
                                    location.reload()
                                }
                            })
                            .catch(error => {
                                // Manejar errores de red u otros errores
                                console.error(error);
                            });
                    }
                });
            });
        }


        function filtroCanales(value, client) {

            fetch('{{ route('estrategia.filter-strategy') }}', {
                method: 'POST',
                body: JSON.stringify({
                    canal: value,
                    client: client,
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                var tabla = ''
                tabla += `<table id='tabla_eliminados' class="table table-sm table-bordered mb-0">
                    <table id='tabla_eliminados' class="table table-sm table-bordered mb-0">
                        <thead class="table-dark text-uppercase text-center">
                        <th width='10%' class="align-middle">Canal</th>
                        <th class="align-middle">Criterio</th>
                        <th width='12%' class="align-middle">Fecha activación</th>
                        <th width='12%' class="align-middle">Hora activación</th>
                        <th width='15%' class="align-middle">Avance</th>
                    </thead><tbody class="text-center">`
                for (let d in data) {
                    tabla += `<tr>
                        <td class="align-middle">${data[d].canal}</td>
                        <td class="align-middle">${data[d].onlyWhere}</td>
                        <td class="align-middle">${data[d].activation_date}</td>
                        <td class="align-middle">${data[d].activation_time}</td>
                        <td class="align-middle"><div class="progress" role="progressbar" aria-label="Animated striped example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar progress-bar-striped progress-bar-animated"id="progres" style="width: 75%"><span id="texto_progress"></span></div></div></td>
                        </tr>`
                }
                tabla += `</tbody></table>`
                document.querySelector('#tabla_eliminados').innerHTML = tabla
                if (value === 'refresh') {
                    document.querySelector('#selectFiltro').value = ''
                }
            });
        }
    </script>

@endsection
