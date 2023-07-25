@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-back')
    <x-btn-standar type='a' name='Regresar' title='Regresar' color="dark" sm='sm' icon='chevron-circle-left'
        href="{{ route($config_layout['btn-back'], $client->id) }}" />
@endsection


@section('content')
    <div hidden id="spinner"></div>
    @if ($message = Session::get('message'))
        <div class="col-12 mb-3">
            <div class="alert alert-{{ $message['type'] }} alert-dismissible show mb-0" role="alert" id='alerta'>
                @if ($message['type'] === 'success')
                    <i class="fas fa-check-circle"></i>
                @else
                    <i class="fas fa-exclamation-triangle"></i>
                @endif
                <span id='mensaje'><strong>{{ $message['message'] }}</strong></span>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

    @endif
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
            @if ($channels_config != null)
                @foreach ($channels as $key => $val)
                    @if (isset($channels_config['channels'][$key]))
                        <div class="col-4">
                            <i class="fas fa-check text-success"></i>&nbsp;
                            {{ strtoupper($val['name']) }}
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


        <div class="alert alert-dismissible fade show d-none" role="alert">
            <span id='messages'></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <table class="table table-sm table-bordered mb-0">
            <thead class="table-dark text-uppercase text-center">
                <th width='3%' class="align-middle ">#</th>
                <th width='7%' class="align-middle ">Canal</th>
                <th class="align-middle" width='3%'>Cobertura</th>
                <th class="align-middle" width='3%'>Registros</th>
                <th class="align-middle" width='3%'>Repetidos</th>
                <th class="align-middle">Criterio</th>
                <th width='3%' class="align-middle">Acciones</th>
            </thead>
            <tbody class="align-middle">
                @foreach ($datas as $k => $data)
                    @if ($data['type'] === 1)
                        <tr>
                            <td class="text-center align-middle">{{ ++$k }}</td>
                            <td class="text-center align-middle">
                                {{ $data['canal'] }}
                            </td>
                            <td class="text-center align-middle">{{ number_format($data['cobertura'], 2, ',', '.') }}%</td>
                            
                                <td class="text-center align-middle">
                                    {{ number_format($data['registros_unicos'], 0, ',', '.') }}

                                </td>
                                
                                <td class="text-center align-middle">
                                    {{ number_format($data['registros_repetidos'], 0, ',', '.') }}
                                </td>
                            <td>{{ $data['onlyWhere'] }}</td>
                            <td class="text-center align-middle">
                                <div class="btn-group" role="group" aria-label="Basic example">

                                    <x-btn-standar type='a' title='Aceptar' color="success" sm='sm'
                                        icon='check-circle' onclick="acceptedStrategy({{ $data['id'] }})" />

                                    <x-btn-standar type='a' title='Eliminar' color="danger" sm='sm'
                                        icon='times-circle' extraclass='eliminar-estrategia'
                                        dataid="{{ $data['id'] }}" />


                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            @if (isset($suma_total))
                <tfoot class="text-center">
                    <tr>
                        <td></td>
                        <td></td>
                        <th>{{ number_format($porcentaje_total, 2, ',', '.') }}%</th>
                        <th>{{ number_format($suma_total, 0, ',', '.') }}</th>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </x-cards>

    <x-cards size='8' xtrasclass='mt-3' header="Nueva estrategia" titlecolor='success'>
        <div class="row">
            <div class="col-12">
                <table class="table table-borderless">
                    {!! Form::open([
                        'route' => 'estrategia.save-estrategia',
                        'method' => 'POST',
                        'id' => 'myForm',
                    ]) !!}
                    <tr>
                        <th class="text-uppercase align-middle" scope="col">Canal: </th>
                        <th>
                            <select onchange='aceptaRepetidos()' class="form-select form-select-sm" name="channels"
                                id="canalsito">
                                <option value="">Seleccione</option>
                                @for ($i = 0; $i < count($channels); $i++)
                                    @if (in_array($i, $ch_approve))
                                        <option value="{{ $i }}">{{ strtoupper($channels[$i]['name']) }}
                                        </option>
                                    @endif
                                @endfor
                            </select>
                        </th>
                    </tr>

                    <tr>
                        <th class="text-uppercase align-middle" scope="col">
                            <a type="button" class="btn btn-success btn-sm" id='btnNuevo'
                                onclick="addRow('{{ $client->prefix }}')">
                                <i class="fas fa-plus-circle"></i>
                                Agregar nuevo campo
                            </a>
                        </th>
                    </tr>
                </table>
                <table id="myTable" class="table-sm table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" width='3%'>Eliminar</th>
                            <th class="align-middle" width='15%'>Campo</th>
                            <th class="align-middle" width='20%'>Operador</th>
                            <th class="align-middle">Valor</th>
                        </tr>
                    </thead>
                </table>

                <table class="table table-borderless">
                    <tr>
                        <th colspan="2" class="text-uppercase align-middle" scope="col">
                            <textarea class="form-control" id="showQue" name="showQue" disabled></textarea>
                            <input type="hidden" name='prefix' id='prefix' value={{ $client->prefix }}>
                            <input type="hidden" name='onlyWhere' id='onlyWhere'>
                            <input type="hidden" name='table_name' id='table_name2'>
                            <input type="hidden" name='location' value='diseno'>
                            <input type="hidden" id="cober" name='cober'>
                            <input type="hidden" id="unic" name='unic'>
                            <input type="hidden" id="repe" name='repe'>
                            <input type="hidden" id="tota" name='total'>
                            <input type="hidden" id="registros" name='registros'>
                            <input type="hidden" id="id_cliente" name='id_cliente' value={{ $client->id }}>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-uppercase align-middle" scope="col">
                            <div class="btn-group" role="group">
                                <x-btn-standar type='submit' id='guard' disabled='true' name='Guardar'
                                    extraclass="mb-0" title='Guardar' color="success" sm='sm' icon='save' />
                                <x-btn-standar type='a' name='Probar' title='Probar' extraclass="mb-0"
                                    color="primary" sm='sm' id='probar' disabled='true' icon='play-circle'
                                    onclick="probarConsulta()" />
                            </div>
                        </th>
                    </tr>
                    {!! Form::close() !!}
                </table>
            </div>
        </div>
    </x-cards>
    <x-cards size='4' xtrasclass='mt-3' header="Estimados" titlecolor='success'>
        <table class="table table-sm table-bordered mb-0 table-condensed">
            <thead class="table-dark text-center text-uppercase">
                <tr>
                    <th>cobertura</th>
                    <th>unicos</th>
                    <th>repetidos</th>
                    <th>total</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td><span id='cobertura'></span></td>
                    <td><span id='unicos'></span></td>
                    <td><span id='repetidos'></span></td>
                    <td><span id="total"></span></td>
                </tr>
            </tbody>
        </table>
    </x-cards>
@endsection


@section('js')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        var i = 0;

        document.getElementById("myForm").addEventListener('submit', validar);

        const enlacesElement = document.querySelectorAll('.eliminar-estrategia');

        const spinner = document.getElementById("spinner");


        if (enlacesElement !== null) {
            enlacesElement.forEach((enlaceElement) => {
                enlaceElement.addEventListener('click', (event) => {
                    const confirmacion = confirm('¿Desea eliminar la estrategia?');
                    if (!confirmacion) {
                        event.preventDefault();
                    } else {
                        fetch(`http://apiest.konecsys.com:8080/estrategia/eliminar/${enlaceElement.dataset.identificador}`, {
                                method: 'DELETE',
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
                                    alert('Eliminado con exito')
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


        function probarConsulta() {
            document.getElementById('guard').disabled = true;
            spinner.removeAttribute('hidden');
            var query = document.getElementById('showQue').value;
            var prefix = document.getElementById('prefix').value;
            var table_name = document.getElementById('table_name2').value;
            var id_cliente = document.getElementById('id_cliente').value;



            let opciones = {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            };

            fetch('{{ route('estrategia.probar-strategy') }}', {
                method: 'POST',
                body: JSON.stringify({
                    query: query,
                    prefix: prefix,
                    table_name: table_name,
                    check: 1,
                    id_cliente: id_cliente,
                    channel: document.querySelector('#canalsito').value,
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                spinner.setAttribute('hidden', '');
                document.getElementById('cobertura').innerHTML =
                    `${data.percent_cober.toLocaleString("de-DE", opciones)}%`
                document.getElementById('unicos').innerHTML = data.total_unicos.toLocaleString("de-DE")
                document.getElementById('repetidos').innerHTML = data.total_repetidos.toLocaleString(
                    "de-DE")
                document.getElementById('total').innerHTML = data.total_r.toLocaleString("de-DE")

                document.getElementById('cober').value = data.percent_cober.toFixed(2)
                document.getElementById('unic').value = data.total_unicos
                document.getElementById('repe').value = data.total_repetidos
                document.getElementById('tota').value = data.total_r


                document.getElementById('registros').value = JSON.stringify(data.unicos)

                document.getElementById('guard').disabled = false;

            });
        }


        function aceptaRepetidos() {

            document.getElementById('guard').disabled = true;

            document.getElementById('cobertura').innerHTML = `0,00%`
            document.getElementById('unicos').innerHTML = `0`
            document.getElementById('repetidos').innerHTML = `0`
            document.getElementById('total').innerHTML = `0`

            document.getElementById('cober').value = 0
            document.getElementById('unic').value = 0
            document.getElementById('repe').value = 0
            document.getElementById('tota').value = 0
        }

        function validar(e) {
            const valoresElements = document.querySelectorAll('.valores');
            if (document.getElementById("canalsito").value === '') {
                alert('Debe selccionar el canal para la estrategia.');
                document.getElementById("canalsito").focus()
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
                if (data.result === '201') {
                    alert(data.message)
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

            var estructura = @json($estrc);

            var table = document.getElementById("myTable");
            aceptaRepetidos()

            document.getElementById('guard').disabled = true;

            var row = table.insertRow(-1);
            row.id = 'tr_' + i

            if (estructura.length > 0) {
                var cell2 = row.insertCell(-1);
                var cell3 = row.insertCell(-1);
                var cell4 = row.insertCell(-1);

                lines = `
                    <select id="my-select" class='form-select form-select-sm campo' onchange="selectInputType(this.value, ${i}, event, document.getElementById('operator_${i}'))">
                    <option>Seleccione</option>`
                for (let d in estructura) {
                    lines +=
                        `<option value='${estructura[d].COLUMN_TYPE}-${estructura[d].TABLE_NAME}-${estructura[d].COLUMN_NAME}'>${estructura[d].NAME}</option>`
                }
                lines += `</select>`

                cell2.innerHTML =
                    `<input type="hidden" id="name_table" />
                    <a onclick="borrarRow(this)" class="btn btn-sm btn-block mb-0 btn-danger mb-0"><i class="fas fa-minus-circle"></i></a>`;
                cell2.className = "text-center align-middle bg-danger p-0"

                cell3.innerHTML = lines
                cell3.id = "td_" + i

                cell4.innerHTML = `
                    <select class='form-select form-select-sm operator' id='operator_${i}'>
                        <option>Seleccione</option>
                        <option value="=" > Igual </option>
                        <option value=">=" > Mayor ó igual </option>
                        <option value="<=" > Menor ó igual </option>
                        <option value=">" > Mayor qué </option>
                        <option value="<" > Menor qué </option>
                        </select>`
            }
            i++
        }

        function borrarRow(x) {
            var i = x.parentNode.parentNode.rowIndex;
            document.getElementById("myTable").deleteRow(i);
            aceptaRepetidos()
        }


        function showQuery() {
            document.getElementById('guard').disabled = true;
            document.getElementById('probar').disabled = false;

            var query = "";
            var queryParts = [];
            const valoresElements = document.querySelectorAll('.valores');
            const name_table = document.querySelector('#name_table');
            const op = document.querySelectorAll('.operator');
            const valoresObj = {};


            const campo = document.querySelectorAll('.campo');

            valoresElements.forEach((element, i) => {

                var dato = campo[i].value
                var openingParenIndex3 = dato.split("-");

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
                    if (op[i].value === 'like') {
                        queryParts.push(`${openingParenIndex3[2]} like '%${element.value}%'`); // 
                    } else if (element.type === 'date' || element.type === 'text') {
                        queryParts.push(`${openingParenIndex3[2]} ${op[i].value} '${element.value}'`); // 
                    } else {
                        queryParts.push(`${openingParenIndex3[2]} ${op[i].value} ${element.value}`); // 
                    }

                }
            });

            query = queryParts.join(' and '); //añadimos los and a la consulta
            document.getElementById('showQue').value = query; // muestro en el textarea el codigo
            document.getElementById('onlyWhere').value = query; // muestro en el textarea el codigo
            document.getElementById('table_name2').value = name_table.value; // muestro en el textarea el codigo
        }

        function selectInputType(value, i, e, op = '') {

            document.getElementById('guard').disabled = true;

            const pattern = /\((\d+)\)/;
            const matches = value.match(pattern);
            var text = ''

            if (matches) {
                const openingParenIndex = value.indexOf("(");
                text = value.substring(0, openingParenIndex);
            } else {
                text = value
            }
            var openingParenIndex2 = value.split("-");
            document.getElementById("name_table").value = openingParenIndex2[1];
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
                } else {
                    nuevoTd.appendChild(nuevoInput);
                    ultimaFila.appendChild(nuevoTd);
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
