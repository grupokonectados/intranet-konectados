@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')

    <a href="{{ route($config_layout["btn-back"]) }}" class="btn btn-dark btn-sm">
        <i class="fas fa-chevron-circle-left"></i>
        Regresar
    </a>

@endsection



@section('content')
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="text-white bg-gray-900">
                        <tr>
                            <th class="text-uppercase text-center align-middle" scope="col">Cliente</th>
                            <th scope="col">
                                <select class="form-select" onchange="selectClient(this.value)" name=""
                                    id="cli">
                                    <option value="">Seleccione</option>
                                    @foreach ($data as $d)
                                        <option value='{{ $d->prefix }}'>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3" id="divsito" style="display: none">
        <div class="card bg-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-4">
                        <button class="btn btn-success btn-sm" onclick="addRow(document.querySelector('#cli').value)">
                            <i class="fas fa-plus-circle"></i>
                            Agregar nuevo campo
                        </button>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="form-check form-switch p-0">
                            <input class="form-check-input" id="check" type="checkbox">
                            <label for="form-check-label">¿Desea que para la estrategia se repitan los usuarios?</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
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

        <div class="card mt-3 bg-white">
            <div class="card-body">
                <div>
                    <button type="button" class="btn btn-success mb-3 btn-sm" onclick="showQuery()">
                        <i class="fas fa-search"></i>
                        Mostrar consulta </button>
                </div>
                {!! Form::open([
                    'route' => 'estrategia.save-estrategia',
                    'method' => 'POST',
                    'novalidate',
                    'id' => 'myForm',
                ]) !!}
                <div>
                    <textarea class="form-control" id="showQue" name="showQue" disabled></textarea>
                    <input type="hidden" name='query_text' id='query_text'>
                    <input type="hidden" name='prefix' id='prefix'>
                    <input type="hidden" name='onlyWhere' id='onlyWhere'>
                    <input type="hidden" name='table_name' id='table_name2'>
                    <input type="hidden" name='repeatUsers' id='repeatUsers'>
                    <input type="hidden" name='location' value='create'>
                </div>
                <div class="my-3">
                    <label for="">Canales</label>
                    <select class="form-control" name="channels" id="">
                        <option value="">Seleccione</option>
                        <option value="1">SMS</option>
                        <option value="2">Agentes</option>
                        <option value="3">Email</option>
                    </select>
                </div>
                <div class="">
                    <label for="">Descripcion</label>
                    <textarea class="form-control" name='query_description'></textarea>
                </div>

                <div class="mt-3">
                    <button class="btn btn-success btn-sm mb-0" type="submit">
                        <i class="fas fa-save"></i>
                        Guardar</button>
                </div>

                {!! Form::close() !!}

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

        function addRow(prefix) {
            selectClient(prefix)
        }

        function selectClient(prefix) {
            console.log(prefix)
            var lineas = "";
            var lines = ''

            fetch('/clients/search-client', {
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
                var table = document.getElementById("myTable");
                var row = table.insertRow(-1);
                row.id = 'tr_' + i
                if (data.length > 0) {
                    document.querySelector('#divsito').style.display = 'block'

                    var cell2 = row.insertCell(-1);
                    var cell3 = row.insertCell(-1);
                    lines = `
                    <select id="my-select" class='form-select' onchange="selectInputType(this.value, ${i}, event)">
                    <option>Seleccione</option>`
                    for (let d in data) {
                        lines +=
                            `<option value='${data[d].COLUMN_TYPE}-${data[d].TABLE_NAME}'>${data[d].COLUMN_NAME}</option>`
                    }
                    lines += `</select>`
                    cell3.innerHTML = lines
                    cell3.id = "td_" + i

                    cell2.innerHTML =
                        '<input type="hidden" id="name_table" /><a onclick="borrarRow(this)" class="btn btn-block mb-0 btn-danger mb-0"><i class="fas fa-minus-circle"></i></a>';
                    cell2.className = "text-center align-middle bg-danger"
                }

                i++
            });
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


            
            document.getElementById('check').checked === true ? document.getElementById('repeatUsers').value = 1 : document.getElementById('repeatUsers').value = 0
            query = queryParts.join(' and '); //añadimos los and a la consulta
            var query2 = 'select * from ' + name_table.value + ' where ' + queryParts.join(' and ');
            document.getElementById('showQue').value = query; // muestro en el textarea el codigo
            document.getElementById('onlyWhere').value = query; // muestro en el textarea el codigo
            document.getElementById('table_name2').value = name_table.value; // muestro en el textarea el codigo
            document.getElementById('query_text').value =
                query2; // asigno a un hidden en el form el valor de la query para poder guardarlo
            document.getElementById('prefix').value = document.getElementById('cli')
                .value; // asigno a un hidden en el form el valor de la del prefix para poder guardarlo
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
                nuevoTd.appendChild(nuevoInput);
                ultimaFila.appendChild(nuevoTd);

                const nuevoInput2 = document.createElement("input");
                nuevoInput2.type = "number";
                nuevoInput2.name = e.target.selectedOptions[0].text + '_max'
                nuevoInput2.className = 'form-control form-control-sm valores limite-input'
                nuevoInput2.setAttribute("data-limite", matches[1]);
                nuevoTd.appendChild(nuevoInput2);
                ultimaFila.appendChild(nuevoTd);

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
                let selectComuna =
                    `<select class="form-select valores" name="${e.target.selectedOptions[0].text}" ><option>Seleccione la comuna</option>`
                for (let i in objComunas) {
                    selectComuna += `<option value="${objComunas[i]}">${objComunas[i]}</option>`
                }
                selectComuna += `</select>`
                nuevoTd.innerHTML = selectComuna;
                ultimaFila.appendChild(nuevoTd);
            } else {
                nuevoInput.name = e.target.selectedOptions[0].text
                nuevoInput.className = 'form-control form-control-sm valores limite-input'
                nuevoInput.setAttribute("data-limite", matches[1]);
                nuevoTd.appendChild(nuevoInput);
                ultimaFila.appendChild(nuevoTd);

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
