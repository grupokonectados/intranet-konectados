@extends('layouts.app')

@section('content')
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="text-white bg-gray-900">
                        <tr>
                            <th class="text-uppercase" scope="col">Cliente</th>
                            <th scope="col">
                                <select class="form-select" onchange="selectClient(this.value)" name="" id="cli">
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
                    <div class="col-12 mb-4">
                        <button class="btn btn-success" onclick="addRow(document.querySelector('#cli').value)">Add</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="myTable" class="table-sm table table-bordered">
                            <thead>
                                <tr>
                                    <th width='5%'>Eliminar</th>
                                    <th width='20%'>Campo</th>
                                    <th>Valor</th>
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
                            <button type="button" class="btn btn-success mb-3" onclick="showQuery()">Show</button>
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
                            </div>
                            <div class="my-3">
                                <label for="">Canales</label>
                                <select class="form-control" name="channels" id="">
                                    <option value="">Seleccione</option>
                                    <option value="1">SMS</option>
                                    <option value="2">Llamada</option>
                                    <option value="3">Email</option>
                                </select>
                            </div>
                            <div class="">
                                <label for="">Descripcion</label>
                                <textarea class="form-control" name='query_description'></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit">Guardar</button>
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

        function addRow(prefix) {
            selectClient(prefix)
        }

        function selectClient(prefix) {
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
                        lines += `<option value='${data[d].COLUMN_TYPE}-${data[d].TABLE_NAME}'>${data[d].COLUMN_NAME}</option>`
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
                        const betweenClause = `monto BETWEEN ${montoMin} AND ${montoMax}`; //crearmos la linea del between
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
                    queryParts.push(`${element.name} = '${element.value}'`); // 
                }
            });

            query = queryParts.join(' and '); //añadimos los and a la consulta
            document.getElementById('showQue').value = query; // muestro en el textarea el codigo

            document.getElementById('query_text').value = query; // asigno a un hidden en el form el valor de la query para poder guardarlo
            document.getElementById('prefix').value = document.getElementById('cli').value; // asigno a un hidden en el form el valor de la del prefix para poder guardarlo
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
            const text2 = value.slice(openingParenIndex2+1);

            
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
                nuevoInput.className = 'form-control form-control-sm valores'
                nuevoInput.setAttribute("maxlength", matches[1]);
                nuevoTd.appendChild(nuevoInput);
                ultimaFila.appendChild(nuevoTd);
                

                const nuevoInput2 = document.createElement("input");
                nuevoInput2.type = "number";
                nuevoInput2.name = e.target.selectedOptions[0].text + '_max'
                nuevoInput2.className = 'form-control form-control-sm valores'
                nuevoInput2.setAttribute("maxlength", matches[1]);
                nuevoTd.appendChild(nuevoInput2);
                ultimaFila.appendChild(nuevoTd);
                

            } else {
                nuevoInput.name = e.target.selectedOptions[0].text
                nuevoInput.className = 'form-control form-control-sm valores'
                nuevoInput.setAttribute("maxlength", matches[1]);
                nuevoTd.appendChild(nuevoInput);
                ultimaFila.appendChild(nuevoTd);
            }





        }
    </script>
@endsection
