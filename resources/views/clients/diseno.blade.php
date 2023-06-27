@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <a href="{{ route($config_layout['btn-back'], $client->id) }}" class="btn btn-dark btn-sm">
        <i class="fas fa-chevron-circle-left"></i>
        Regresar
    </a>
@endsection


@section('content')
    <div class="col-8 mb-3">
        <div class="card ">
            <div class="card-header">

                <h5 class="mb-0">Datos del cliente</h5>
            </div>
            <div class="card-body ">
                <div class="d-flex justify-content-between">
                    <div class="col-6">
                        <label class="form-label col-6">Nombre: {{ $client->name }}</label>

                        <label class="form-label form-label col-6">Prefix: {{ $client->prefix }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4 mb-3">
        <div class="card ">
            <div class="card-header">

                <h5 class="mb-0">Canales Permitidos</h5>
            </div>
            <div class="card-body ">
                <div class="row">

                    @foreach ($channels as $key => $val)
                        @if (isset($client->active_channels[$key]))
                            <div class="col-4">{{ $val }}</div>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="col-12 my-3">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">Estrategias</h5>
            </div>
            <div class="card-body ">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <th class="align-middle text-center">Canal</th>
                        <th class="align-middle text-center">Descripcion</th>
                        <th width='3%' class="align-middle text-center">Acciones</th>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td>
                                    @if (isset($channels[$data->channels]))
                                        {{ $channels[$data->channels] }}
                                    @endif
                                </td>
                                <td>{{ $data->onlyWhere }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a title="Aceptar" onclick="acceptedStrategy({{ $data->id }})"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle"></i>
                                        </a>
                                        {{-- <a class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a> --}}
                                        <a title="Eliminar" href="{{ route('estrategia.delete-strategy', $data->id) }}"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-times-circle"></i>

                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if (count($datas) > 0)
                    <button type="button" onclick="probar('{{ $client->prefix }}')" class="btn btn-sm btn-info mt-3">Probar
                        estrategias</button>
                @endif

            </div>
        </div>
    </div>

    <div id="divv" style="display: none;" class="col-12 my-3">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">Estrategias de prueba</h5>
            </div>
            <div class="card-body" id="div-p">
            </div>
        </div>
    </div>



    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Nueva estrategia</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width=45% class="text-uppercase  align-middle" scope="col">Cliente</th>
                                <th class="text-uppercase align-middle" scope="col">{{ $client->name }}
                                </th>
                            </tr>
                            <tr>
                                <th class="text-uppercase  align-middle" scope="col">¿Desea que para la estrategia se
                                    repitan los usuarios?</th>
                                <th class="text-uppercase text-center align-middle" scope="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="check" type="checkbox">
                                    </div>
                                </th>
                            </tr>
                            {!! Form::open([
                                'route' => 'estrategia.save-estrategia',
                                'method' => 'POST',
                                'novalidate',
                                'id' => 'myForm',
                            ]) !!}
                            <tr>
                                <th><label for="">Canales</label></th>
                                <th>
                                    <select class="form-control" name="channels" id="">
                                        <option value="">Seleccione</option>
                                        @foreach ($channels as $key => $val)
                                            @if (in_array($key, $multiples))
                                                <option value="{{ $key }}">{{ $val }}</option>
                                            @endif
                                        @endforeach


                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <label for="">Descripcion</label>
                                </th>
                                <th>
                                    <textarea class="form-control" name='query_description'></textarea>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-uppercase align-middle" scope="col">
                                    <textarea class="form-control" id="showQue" name="showQue" disabled></textarea>
                                    <input type="hidden" name='query_text' id='query_text'>
                                    <input type="hidden" name='prefix' id='prefix' value={{ $client->prefix }}>
                                    <input type="hidden" name='onlyWhere' id='onlyWhere'>
                                    <input type="hidden" name='table_name' id='table_name2'>
                                    <input type="hidden" name='repeatUsers' id='repeatUsers'>
                                    <input type="hidden" name='location' value='diseno'>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-uppercase align-middle" scope="col">
                                    <button class="btn btn-success btn-sm mb-0" type="submit">
                                        <i class="fas fa-save"></i>
                                        Guardar</button>
                                </th>
                            </tr>
                            {!! Form::close() !!}
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-bordered">
                            <tr>
                                <th class="text-uppercase align-middle" scope="col">
                                    <button class="btn btn-success btn-sm" onclick="addRow('{{ $client->prefix }}')">
                                        <i class="fas fa-plus-circle"></i>
                                        Agregar nuevo campo
                                    </button>
                                </th>
                            </tr>
                        </table>

                        <table id="myTable" class="table-sm table table-bordered">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center" width='5%'>Eliminar</th>
                                    <th class="align-middle text-center" width='20%'>Campo</th>
                                    <th class="align-middle text-center">Valor</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var i = 0;

        const objComunas = {
            1: 'Cerro Navia',
            2: 'Conchalí',
            3: 'El Bosque',
            4: 'Estación Central',
            5: 'Huechuraba',
            6: 'Independencia',
            7: 'La Cisterna',
            8: 'La Granja',
            9: 'La Florida',
            10: 'La Pintana',
            11: 'La Reina',
            12: 'Las Condes',
            13: 'Lo Barnechea',
            14: 'Lo Espejo',
            15: 'Lo Prado',
            16: 'Macul',
            17: 'Maipú',
            18: 'Ñuñoa',
            19: 'Pedro Aguirre Cerda',
            20: 'Peñalolén',
            21: 'Providencia',
            22: 'Pudahuel',
            23: 'Quilicura',
            24: 'Quinta Normal',
            25: 'Recoleta',
            26: 'Renca',
            27: 'San Miguel',
            28: 'San Joaquín',
            29: 'San Ramón',
            30: 'Santiago',
            31: 'Vitacura'
        }

        function acceptedStrategy(id){
            

            fetch('{{ route("estrategia.accepted-strategy") }}', {
                method: 'POST',
                body: JSON.stringify({
                    id: id
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                // Recargar la página actual

                console.log(data)
                if(data.result === 1){
                    alert(data.message)
                    location.reload()
                }else{
                    alert(data.message)
                }
            });

        }

        function probar(prefix) {
            var tabla = ''
            fetch('/clients/probar-consulta', {
                method: 'POST',
                body: JSON.stringify({
                    prefix: prefix
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                var dat1 = data.data4.results_querys
                var testt = []

                var repetidos = 0
                for (let r in dat1) {
                    testt[r] = dat1[r].length
                }


                repetidos = testt.reduce((acc, curr) => acc - curr);


                tabla += `<table class="table table-sm table-bordered mb-0">
                            <thead>
                            <th class="align-middle text-center">Canal</th>
                            <th class="align-middle" width='3%'>Cobertura</th>
                            <th class="align-middle" width='3%'>Registros</th>
                            <th width='7%'>¿Acepta repetidos?</th>
                            <th class="align-middle" width='3%'>Repetidos</th>
                            <th class="align-middle text-center">Descripcion</th>
                            </thead><tbody>`
                for (d in data.data1) {
                    tabla += `<tr>
                            <td class="text-center">${data.data1[d].title}</td>
                            <td class="text-center">${data.data1[d].porcentaje}%</td>
                            <td class="text-center">${data.data1[d].datos}<td>`
                    if (data.data3[d].repeatUsers === 1) {
                        tabla += `<div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">Si</label>
                                    <input class="form-check-input ml-0" disabled checked id="check"
                                    type="checkbox">
                                    </div>
                                    </td>
                                    <td>${repetidos}</td>`
                    } else {
                        tabla += ` <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">No</label>
                                    <input class="form-check-input ml-0" disabled id="check" type="checkbox">
                                    </div>
                                    <td>0</td>`
                    }
                    tabla += `
                                
                                <td>${data.data3[d].onlyWhere}</td></tr>`
                }
                tabla += `<tr>
                            <td></td>
                            <td class="text-center">${data.data2.porcentual}%</td>
                            <td class="text-center">${data.data2.total}</td>
                            <td colspan="6"></td>
                            </tbody>
                            </table>`

                document.querySelector('#divv').style.display = 'block'
                document.querySelector('#div-p').innerHTML = tabla
            });

        }

        function addRow() {

            var estructura = @json($estructura)

            var table = document.getElementById("myTable");
            var row = table.insertRow(-1);

            row.id = 'tr_' + i


            if (estructura.length > 0) {
                var cell2 = row.insertCell(-1);
                var cell3 = row.insertCell(-1);

                lines = `
                    <select id="my-select" class='form-select' onchange="selectInputType(this.value, ${i}, event)">
                    <option>Seleccione</option>`
                for (let d in estructura) {
                    lines +=
                        `<option value='${estructura[d].COLUMN_TYPE}-${estructura[d].TABLE_NAME}'>${estructura[d].COLUMN_NAME}</option>`
                }
                lines += `</select>`
                cell3.innerHTML = lines
                cell3.id = "td_" + i

                var celdaExistente = document.getElementById("td2_" + i);


                cell2.innerHTML =
                    '<input type="hidden" id="name_table" /><a onclick="borrarRow(this)" class="btn btn-block mb-0 btn-danger mb-0"><i class="fas fa-minus-circle"></i></a>';
                cell2.className = "text-center align-middle bg-danger"

            }

            i++
        }



        function borrarRow(x) {
            var i = x.parentNode.parentNode.rowIndex;
            document.getElementById("myTable").deleteRow(i);
        }

        function showQuery() {
            var query = "";
            var queryParts = [];
            const valoresElements = document.querySelectorAll('.valores');
            const name_table = document.querySelector('#name_table');
            const valoresObj = {};

            valoresElements.forEach((element) => {
                if (element.name === 'monto_min' && element.value !== '') { //Verificamos el campo monto
                    const montoMin = parseFloat(element.value);
                    const montoMax = parseFloat(document.querySelector('[name="monto_max"]').value);
                    if (!isNaN(montoMin) && !isNaN(montoMax)) {
                        const betweenClause =
                            `monto BETWEEN ${montoMin} AND ${montoMax}`; //crearmos la linea del between
                        if (!queryParts.includes(betweenClause)) {
                            queryParts.push(betweenClause); // lo metemos en el objeto
                        }
                    }
                } else if (element.name === 'monto_max' && element.value !== '') {
                    const montoMin = parseFloat(document.querySelector('[name="monto_min"]').value);
                    const montoMax = parseFloat(element.value);
                    if (!isNaN(montoMin) && !isNaN(montoMax)) {
                        const betweenClause = `monto BETWEEN ${montoMin} AND ${montoMax}`;
                        if (!queryParts.includes(betweenClause)) {
                            queryParts.push(betweenClause);
                        }
                    }
                } else {
                    queryParts.push(`${element.name} like '%${element.value}%'`); // 
                }
            });

            document.getElementById('check').checked === true ? document.getElementById('repeatUsers').value = 1 : document
                .getElementById('repeatUsers').value = 0
            query = queryParts.join(' and '); //añadimos los and a la consulta
            var query2 = 'select * from ' + name_table.value + ' where ' + queryParts.join(' and ');
            document.getElementById('showQue').value = query; // muestro en el textarea el codigo
            document.getElementById('onlyWhere').value = query; // muestro en el textarea el codigo
            document.getElementById('table_name2').value = name_table.value; // muestro en el textarea el codigo
            document.getElementById('query_text').value =
                query2; // asigno a un hidden en el form el valor de la query para poder guardarlo
        }

        function selectInputType(value, i, e) {

            const pattern = /\((\d+)\)/;
            const matches = value.match(pattern);
            var text = ''

            if (matches) {
                const openingParenIndex = value.indexOf("(");
                text = value.substring(0, openingParenIndex);
            } else {
                text = value
            }

            const openingParenIndex2 = value.indexOf("-");
            const text2 = value.slice(openingParenIndex2 + 1);
            document.getElementById("name_table").value = text2;
            var table = document.getElementById("myTable");
            nuevoTd = document.createElement("td");
            var nuevoDiv = document.createElement("div");
            nuevoDiv.className = 'input-group'

            nuevoTd.id = 'td2_' + i


            const ultimaFila = table.rows[table.rows.length - 1];
            const nuevoInput = document.createElement("input");

            switch (text) {
                case 'varchar':
                    nuevoInput.type = "text";
                    break;
                case 'int':
                    nuevoInput.type = "number";
                    break;
                case 'date':
                    nuevoInput.type = "date";
                    break;
            }

            if (e.target.selectedOptions[0].text === 'monto') {
                nuevoInput.name = e.target.selectedOptions[0].text + '_min'
                nuevoInput.className = 'form-control form-control-sm valores limite-input'
                nuevoInput.setAttribute("data-limite", matches[1]);
                nuevoInput.setAttribute("onkeyup", 'showQuery()');
                


                const nuevoInput2 = document.createElement("input");
                nuevoInput2.type = "number";
                nuevoInput2.name = e.target.selectedOptions[0].text + '_max'
                nuevoInput2.className = 'form-control form-control-sm valores limite-input'
                nuevoInput2.setAttribute("data-limite", matches[1]);
                nuevoInput.setAttribute("placeholder", 'minimo');
                nuevoInput2.setAttribute("placeholder", 'maximo');
                nuevoInput2.setAttribute("onkeyup", 'showQuery()');
                if (document.getElementById('td2_' + i)) {
                    document.getElementById('td2_' + i).innerHTML = ''
                    nuevoDiv.appendChild(nuevoInput)
                    nuevoDiv.appendChild(nuevoInput2)
                    document.getElementById('td2_' + i).appendChild(nuevoDiv)
                } else {
                    nuevoDiv.appendChild(nuevoInput)
                    nuevoDiv.appendChild(nuevoInput2)
                    ultimaFila.appendChild(nuevoDiv);
                }

                const campos = document.querySelectorAll(".limite-input");
                campos.forEach(function(campo) {
                    const limite = campo.dataset.limite;

                    campo.addEventListener("input", function() {
                        if (this.value.length > limite) {
                            this.value = this.value.slice(0, limite);
                        }
                    });
                });

            } else if (e.target.selectedOptions[0].text === 'comuna') {
                var selectComuna =
                    `<select class="form-select valores" onkeyup="showQuery()" name="${e.target.selectedOptions[0].text}" ><option>Seleccione la comuna</option>`
                for (let i in objComunas) {
                    selectComuna += `<option value="${objComunas[i]}">${objComunas[i]}</option>`
                }
                selectComuna += `</select>`

                if (document.getElementById('td2_' + i)) {
                    document.getElementById('td2_' + i).innerHTML = ''
                    document.getElementById('td2_' + i).innerHTML = selectComuna;
                } else {
                    ultimaFila.appendChild(nuevoTd);
                    document.getElementById('td2_' + i).innerHTML = selectComuna;
                }
            } else {
                nuevoInput.name = e.target.selectedOptions[0].text
                nuevoInput.className = 'form-control form-control-sm valores limite-input'
                nuevoInput.setAttribute("data-limite", matches[1]);
                nuevoInput.setAttribute("onkeyup", 'showQuery()');
                if (document.getElementById('td2_' + i)) {
                    document.getElementById('td2_' + i).innerHTML = ''
                    document.getElementById('td2_' + i).appendChild(nuevoInput)
                    console.log(document.getElementById('td2_' + i))
                } else {
                    nuevoTd.appendChild(nuevoInput);
                    ultimaFila.appendChild(nuevoTd);
                    console.log(document.getElementById('td2_' + i))
                }

                const campos = document.querySelectorAll(".limite-input");
                campos.forEach(function(campo) {
                    const limite = campo.dataset.limite;

                    campo.addEventListener("input", function() {
                        if (this.value.length > limite) {
                            this.value = this.value.slice(0, limite);
                        }
                    });
                });
            }

        }
    </script>
@endsection
