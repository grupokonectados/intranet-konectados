@extends('layouts.app')

@section('content')
    <div class="col-12 mb-3 ">
        <div class="card bg-white">
            <div class="card-body d-flex">
                <div style="width: 80%"><span>Probar filtro</span></div>
                <div><a href='{{ route('estrategia.index') }}' class="btn btn-dark btn-sm">Regresar</a></div>
                
                
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Descripcion: {{ $data->query_description }}</label>
                    </div>
                    
                    <div class="col-6">
                        <label for="" class="form-label">Canal: @switch($data->channels)
                            @case(1)
                                SMS
                            @break

                            @case(2)
                                Llamada
                            @break

                            @case(3)
                                Email
                            @endswitch</label>
                        
                    </div>
                    <div class="col-12">
                        <label for="" class="form-label">Filtro:</label>
                        <textarea class="form-control"> {{ $data->onlyWhere }}</textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="button"
                            onclick="runQuery( '{{ $data->table_name }}', {{ $data->channels }})"
                            class="btn btn-info">Run</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12 mt-3" style="display: none" id="divsito">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <div class="row">
                    <div class="col-12">
                        <span id="contador"></span>
                    </div>
                    <div class="col-12 my-4">
                        <span id="estadisticas"></span>
                    </div>
                    <div class="col-12">
                        <div id="tabla"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const csrfToken = "{{ csrf_token() }}"; // Declaramos como constante el token CSRF

        function runQuery(table_name, channel) {
            var query = @json($data->query); // convertimos en json los datos de la query para ser probada
            var where = @json($data->onlyWhere); // convertimos en json los datos del where de la consulta
            head_table = '' // declaramos el head vacio porque va a contener un string para luego agregarlo a la tabla para mostrar los nombres de las columnas
            body_table = '' // declaramos el body de la tabla vacio porque va a contener el string con el resultado de la consulta
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

                if(data.result != 0){
                    
                    var canal = '' // declaramos el canal vacio para incluir un string con el canal utilizado
                    if (channel == 1) {
                        canal = 'SMS'
                    } else if(channel == 2){
                        canal = 'movil'
                    }else{
                        canal = 'email'
                    }

                    document.getElementById('divsito').style.display = 'block' // colocamos visible el div
                    document.getElementById('contador').innerHTML = `Total de registros: ${data.contador}` // mostramos un mensaje con el contador
                    // mostramos un mensaje con las estadisticas.
                    document.getElementById('estadisticas').innerHTML = `<p>Porcentaje de cobertura: ${data.porcentaje}%</p>`

                    

                }else{
                    document.getElementById('divsito').style.display = 'block' // colocamos visible el div
                    document.getElementById('divsito').innerHTML = `<div class="card bg-white"><div class="card-body bg-white"><span>${data.message}</span></div></div>`
                }

            });
        }
    </script>
@endsection
