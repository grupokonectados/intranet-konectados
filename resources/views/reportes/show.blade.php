@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')

    <a href="{{ route($config_layout['btn-back']) }}" class="btn btn-dark btn-sm">
        <i class="fas fa-chevron-circle-left"></i>
        Regresar
    </a>

@endsection

@section('content')
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-sm table-bordered mb-0 table-striped-columns">
                    <tr>
                        <td scope='col' class="align-middle table-dark text-uppercase text-center">Descripcion</td>
                        <td class="align-middle">{{ $data->query_description }}</td>
                    </tr>
                    <tr>
                        <td class="align-middle table-dark text-uppercase text-center">Canal</td>
                        <td class="align-middle">
                            @switch($data->channels)
                                @case(1)
                                    SMS
                                @break

                                @case(2)
                                    Llamada
                                @break

                                @case(3)
                                    Email
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle table-dark text-uppercase text-center">Filtro</td>
                            <td class="align-middle">
                                <textarea disabled class="form-control"> {{ $data->onlyWhere }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle table-dark text-uppercase" width="25%">¿Desea que para la estrategia se repitan los usuarios?</td>
                            <td class="align-middle">@if ($data->repeatUsers == 1)
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">Si</label>
                                    <input class="form-check-input ml-0" disabled checked id="check" type="checkbox">
                                </div>
                                @else
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">No</label>
                                    <input class="form-check-input ml-0" disabled  id="check" type="checkbox">
                                </div>
                                @endif</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="pt-4">

                                    <button type="button"
                                        onclick="runQuery( '{{ $data->table_name }}', {{ $data->channels }})"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-play-circle"></i>
                                        Mostrar resultados
                                    </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


        <div class="col-4" style="display: none" id="divsito">
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm table-bordered mb-0 table-striped-columns">
                        <tr  id="contador">
                            
                        </tr>
                        <tr id="estadisticas">
                            
                        </tr>
                    </table>
                    
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script>
            const csrfToken = "{{ csrf_token() }}"; // Declaramos como constante el token CSRF

            const numero_fotmatrado = (number) => {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function runQuery(table_name, channel) {
                var query = @json($data->query); // convertimos en json los datos de la query para ser probada
                var where = @json($data->onlyWhere); // convertimos en json los datos del where de la consulta
                head_table =
                    '' // declaramos el head vacio porque va a contener un string para luego agregarlo a la tabla para mostrar los nombres de las columnas
                body_table =
                    '' // declaramos el body de la tabla vacio porque va a contener el string con el resultado de la consulta
                fetch('/estrategia/run-query', {
                    method: 'POST',
                    body: JSON.stringify({
                        query: query,
                        table_name: table_name,
                        where: where,
                        channel: channel
                    }),
                    headers: {
                        'content-type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).then(response => {
                    return response.json();
                }).then(data => {

                    if (data.result != 0) {

                        var canal = '' // declaramos el canal vacio para incluir un string con el canal utilizado
                        if (channel == 1) {
                            canal = 'SMS'
                        } else if (channel == 2) {
                            canal = 'movil'
                        } else {
                            canal = 'email'
                        }

                        document.getElementById('divsito').style.display = 'block' // colocamos visible el div
                        document.getElementById('contador').innerHTML =
                            `<td class="align-middle table-dark text-uppercase">Total de registros:</td> 
                            <td class="text-center">${numero_fotmatrado(data.contador)}</td>` // mostramos un mensaje con el contador
                        // mostramos un mensaje con las estadisticas.
                        document.getElementById('estadisticas').innerHTML =
                            `<td class="align-middle table-dark text-uppercase">Porcentaje de cobertura:</td> 
                            <td class="text-center" >${data.porcentaje}%</td>` 



                    } else {
                        document.getElementById('divsito').style.display = 'block' // colocamos visible el div
                        document.getElementById('divsito').innerHTML =
                            `<div class="card"><div class="card-body"><span>${data.message}</span></div></div>`
                    }

                });
            }
        </script>
    @endsection
