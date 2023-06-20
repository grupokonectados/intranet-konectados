@extends('layouts.app')



@section('content')
    <div class="col-12 mb-3">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Nombre: {{ $client->name }}</label>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Prefix: {{ $client->prefix }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <div class="col-6 mb-3">
        <div class="card bg-white">
            <div class="card-header">
                <h5 class="mb-0">Estrategias activas</h5>
            </div>
            <div class="card-body bg-white">

                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <th>Canal</th>
                        <th>Covertura</th>
                        <th>Descripcion</th>

                        <th>Estado</th>
                    </thead>
                    <tbody>
                        @foreach ($dataEstrategias as $k => $data)
                            <tr>
                                <td>
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
                                    <td>{{ number_format((count($contador[$k]) / 10000) * 100, 2, ',', '.') }}%</td>
                                    <td>{{ $data->query_description }}</td>
                                    <td>


                                        Activo


                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td>{{ number_format(($contador['total'] / 10000) * 100, 2, ',', '.') }}%</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>



        <div class="col-12 my-3">
            <div class="card bg-white">
                <div class="card-header">
                    <h5 class="mb-0">Estrategias inactivas</h5>
                </div>
                <div class="card-body bg-white">

                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <th>Canal</th>

                            <th>Descripcion</th>

                            <th>Estado</th>
                        </thead>
                        <tbody>
                            @foreach ($dataEstrategiasNot as $k => $data)
                                <tr>
                                    <td>
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

                                        <td>{{ $data->query_description }}</td>
                                        <td>
                                            <button class="btn btn-success">Procesar <i class="fas fa-save"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        @endsection

        @section('js')
            <script>
                const csrfToken = "{{ csrf_token() }}";

                function isActive(id, value) {
                    //console.log(id)

                    fetch('{{ route('estrategia.is-active') }}', {
                        method: 'POST',
                        body: JSON.stringify({
                            id: id,
                            value: value
                        }),
                        headers: {
                            'content-type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).then(response => {
                        return response.json();
                    }).then(data => {
                        console.log(data)

                        if (data.error) {
                            alert(data.error)
                            location.reload();
                        } else {
                            location.reload();
                        }

                        // location.reload();
                    });
                }

                google.charts.load('current', {
                    'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawChart);

                const datax = @json($dataChart)

                function drawChart() {

                    if (datax.length > 0) {
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Canal');
                        data.addColumn('number', 'Cobertura');

                        //var dataArray = <?php echo json_encode($dataChart); ?>;
                        for (var i = 0; i < datax.length; i++) {
                            data.addRow([datax[i].title, datax[i].datos]);
                        }

                        data.addRow(['Total de registros', 10000]);

                        //console.log(data)

                        var options = {
                            title: 'Grafico de cobertura',
                            chartArea: {
                                width: '50%'
                            },
                            hAxis: {
                                title: 'Porcentaje %',
                                minValue: 0
                            },
                            vAxis: {
                                title: 'Canal'
                            },
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
                        chart.draw(data, options);
                    } else {
                        document.getElementById('chart_div').innerHTML = `<span>Nada que mostrar</span>`
                    }
                }
            </script>
        @endsection
