@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], $client->id) }}" />
@endsection


@section('content')
    <x-cards size='8' xtrasclass='mb-3' header="Datos del cliente" titlecolor='primary'>
        <div class="d-flex justify-content-between">
            <div class="col-6">
                <label class="form-label col-6">Nombre: {{ $client->name }}</label>

                <label class="form-label form-label col-6">Prefix: {{ $client->prefix }}</label>
            </div>
        </div>

    </x-cards>

    <x-cards size='4' xtrasclass='mb-3' header="Canales Permitidos" titlecolor='primary'>
        <div class="row">
            @if(count($client->active_channels) >0)
            @foreach ($channels as $key => $val)
                @if (isset($client->active_channels[$key]))
                    <div class="col-4">
                        <i class="fas fa-check text-success"></i>&nbsp;
                        {{ $val }}
                    </div>
                
                @endif
            @endforeach
            @else
                <div class="alert alert-danger alert-dismissible show mb-0" role="alert">
                    No tiene canales Activos.
                </div>
                @endif

        </div>
    </x-cards>
    <x-cards xtrasclass='my-3' header="Estrategias" titlecolor='primary'>


        <div class="alert  alert-dismissible fade show d-none" role="alert">
            <span id='messages'></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>



        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-dark">
                <th width='7%' class="align-middle text-white text-center text-uppercase">Canal</th>
                <th class="align-middle text-white text-uppercase" width='3%'>Cobertura</th>
                <th class="align-middle text-white text-uppercase" width='3%'>Registros</th>
                <th width='7%' class="text-center text-white text-uppercase">¿Acepta repetidos?</th>
                <th class="align-middle text-white text-uppercase" width='3%'>Repetidos</th>
                <th class="align-middle text-white text-uppercase text-center">Criterio</th>
                <th width='3%' class="align-middle text-uppercase text-center text-white">Acciones</th>
            </thead>
            <tbody class="align-middle">
                @foreach ($datas as $k => $data)
                @if($data->isActive === 0)
                    <tr>
                        <td class="text-center align-middle">
                            {{ $data->canal }}
                        </td>
                        <td class="text-center align-middle">{{ $data->porcentaje_registros_unicos }}%</td>
                        <td class="text-center align-middle">{{ $data->total_registros_unicos }}</td>
                        <td class="text-center align-middle">
                            @if ($data->repeatUsers == 1)
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">Si</label>
                                    <input class="form-check-input ml-0" disabled checked type="checkbox">
                                </div>
                            @else
                                <div class="form-check form-switch align-items-stretch">
                                    <label for="form-check-label">No</label>
                                    <input class="form-check-input ml-0" disabled type="checkbox">
                                </div>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($data->repeatUsers == 1)
                                {{ count($data->repetidos) }}
                            @else
                                0
                            @endif
                        </td>
                        <td class="align-middle">{{ $data->onlyWhere }}</td>
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group" aria-label="Basic example">

                                <x-btn-standar type='a' title='Aceptar' color="success" sm='sm'
                                    icon='check-circle' onclick="acceptedStrategy({{ $data->id }})" />

                                {{-- <a class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a> --}}

                                <x-btn-standar type='a' title='Eliminar' color="danger" sm='sm'
                                    icon='times-circle' extraclass='eliminar-estrategia'
                                    href="{{ route('estrategia.delete-strategy', $data->id) }}" />


                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </x-cards>

    <x-cards xtrasclass='mt-3' header="Nueva estrategia" titlecolor='success' >
        <div class="row">
            <div class="col-5">
                <table class="table table-bordered">
                    <tr>
                        <th width=45% class="text-uppercase  align-middle" scope="col">Cliente</th>
                        <th class="text-uppercase align-middle" scope="col">{{ $client->name }}
                        </th>
                    </tr>
                    {!! Form::open([
                        'route' => 'estrategia.save-estrategia',
                        'method' => 'POST',
                        'id' => 'myForm',
                    ]) !!}
                    <tr>
                        <th class="text-uppercase  align-middle" scope="col">¿Desea que para la estrategia se
                            repitan los usuarios?</th>
                        <th class="text-uppercase text-center align-middle" scope="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" id="check" type="checkbox">
                            </div>
                        </th>
                    </tr>

                    <tr>
                        <th class="text-uppercase align-middle" scope="col">Canales</th>
                        <th>
                            <select class="form-select" name="channels" id="canalsito">
                                <option value="">Seleccione</option>
                                @for ($i = 0; $i < count($channels); $i++)
                                    @if (in_array($i, $ch_approve))
                                        <option value="{{ $i }}">{{ $channels[$i] }}</option>
                                    @endif
                                @endfor

                            </select>

                        </th>
                    </tr>
                    <tr>
                        <th class="text-uppercase align-middle" scope="col">
                            Descripcion
                        </th>
                        <th>
                            <textarea class="form-control" id='query_description' name='query_description'></textarea>
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
            <div class="col-7">
                <table class="table table-bordered">
                    <tr>
                        <th class="text-uppercase align-middle" scope="col">
                            <button class="btn btn-success btn-sm" id='btnNuevo'
                                onclick="addRow('{{ $client->prefix }}')">
                                <i class="fas fa-plus-circle"></i>
                                Agregar nuevo campo
                            </button>
                        </th>
                    </tr>
                </table>

                <table id="myTable" class="table-sm table table-bordered">
                    <thead>
                        <tr>
                            <th class="align-middle text-center" width='3%'>Eliminar</th>
                            <th class="align-middle text-center" width='20%'>Operador</th>
                            <th class="align-middle text-center" width='15%'>Campo</th>
                            <th class="align-middle text-center">Valor</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </x-cards>


@endsection


@section('js')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var i = 0;
        
        document.getElementById("myForm").addEventListener('submit', validar);


        const enlacesElement = document.querySelectorAll('.eliminar-estrategia');


        if (enlacesElement !== null) {
            enlacesElement.forEach((enlaceElement) => {
                enlaceElement.addEventListener('click', (event) => {
                    const confirmacion = confirm('¿Desea eliminar la estrategia?');
                    if (!confirmacion) {
                        event.preventDefault();
                    }
                });
            });
        }

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

        function validar(e) {
            const valoresElements = document.querySelectorAll('.valores');
            if (document.getElementById("canalsito").value === '') {
                alert('Debe selccionar el canal para la estrategia.');
                document.getElementById("canalsito").focus()
                e.preventDefault();
                return false
            } else if (document.getElementById("query_description").value === '') {
                alert('Debe ingresar una descripcion para la estrategia.');
                document.getElementById("query_description").focus()
                e.preventDefault();
                return false
            } else if (valoresElements.length === 0) {
                alert('debe haber al menos una consultar para generar la estrategia.');
                document.getElementById("btnNuevo").focus()
                e.preventDefault();
                return false;
            } else {
                return true
            }
        }

        function acceptedStrategy(id) {

            fetch('{{ route('estrategia.accepted-strategy') }}', {
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

                // console.log(data)
                if (data.result === 1) {
                    document.querySelector('.alert').classList.remove('d-none');
                    document.querySelector('.alert').classList.add('alert-success')
                    document.querySelector('#messages').innerHTML = data.message
                    location.reload()
                } else {
                    document.querySelector('.alert').classList.remove('d-none');
                    document.querySelector('.alert').classList.remove('alert-success');
                    document.querySelector('.alert').classList.add('alert-danger')
                    document.querySelector('#messages').innerHTML = data.message
                }
            });

        }


        function addRow() {

            var estructura = @json($estructura);

            var table = document.getElementById("myTable");
            var row = table.insertRow(-1);
            row.id = 'tr_' + i

            if (estructura.length > 0) {
                var cell2 = row.insertCell(-1);
                var cell3 = row.insertCell(-1);
                var cell4 = row.insertCell(-1);

                lines = `
                    <select id="my-select" class='form-select form-select-sm' onchange="selectInputType(this.value, ${i}, event, document.getElementById('operator_${i}'))">
                    <option>Seleccione</option>`
                for (let d in estructura) {
                    lines +=
                        `<option value='${estructura[d].COLUMN_TYPE}-${estructura[d].TABLE_NAME}'>${estructura[d].COLUMN_NAME}</option>`
                }
                lines += `</select>`

                cell2.innerHTML =
                    `<input type="hidden" id="name_table" />
                    <a onclick="borrarRow(this)" class="btn btn-sm btn-block mb-0 btn-danger mb-0"><i class="fas fa-minus-circle"></i></a>`;
                cell2.className = "text-center align-middle bg-danger p-0"

                cell4.innerHTML = lines
                cell4.id = "td_" + i

                cell3.innerHTML = `
                    <select class='form-select form-select-sm operator' id='operator_${i}'>
                        <option>Seleccione</option>
                        <option value="=" > = </option>
                        <option value=">=" > >= </option>
                        <option value="<=" > <= </option>
                        <option value=">" > > </option>
                        <option value="<" > < </option>
                        <option value="!=" > != </option>
                        <option value="dh" > Desde-Hasta</option>
                        <option value="like" > Like </option>
                        </select>`
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
            const op = document.querySelectorAll('.operator');
            const valoresObj = {};

            valoresElements.forEach((element, i) => {

                console.log(element.value)
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
                    if (element.type === 'text' || op[i].value === 'like') {
                        queryParts.push(`${element.name} like '%${element.value}%'`); // 
                    } else if (element.type === 'date' || element.type === 'text') {
                        queryParts.push(`${element.name} ${op[i].value} '${element.value}'`); // 
                    } else {
                        queryParts.push(`${element.name} ${op[i].value} ${element.value}`); // 
                    }

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

        function selectInputType(value, i, e, op = '') {

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
                if (op.value === 'dh') {
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
                } else {
                    nuevoInput.name = e.target.selectedOptions[0].text
                    nuevoInput.className = 'form-control form-control-sm valores limite-input'
                    nuevoInput.setAttribute("data-limite", matches[1]);
                    nuevoInput.setAttribute("onkeyup", 'showQuery()');

                    if (document.getElementById('td2_' + i)) {
                        document.getElementById('td2_' + i).innerHTML = ''
                        nuevoDiv.appendChild(nuevoInput)
                        document.getElementById('td2_' + i).appendChild(nuevoDiv)
                    } else {
                        nuevoDiv.appendChild(nuevoInput)
                        ultimaFila.appendChild(nuevoDiv);
                    }

                }

            } else if (e.target.selectedOptions[0].text === 'comuna') {
                var selectComuna =
                    `<select class="form-select valores" onchange="showQuery()" name="${e.target.selectedOptions[0].text}" ><option>Seleccione la comuna</option>`
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
