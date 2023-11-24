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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {!! Form::open([
                    'route' => 'reports.csv',
                    'method' => 'POST',
                    'novalidate',
                    'id' => 'myForm',
                    'onsubmit' => 'return validarFormulario()',
                ]) !!}
                <input type="hidden" name="client_id" value="{{ $id }}">

                <div class="my-3">
                    <label for="">Fecha</label>
                    <input class="form-control" max='{{ date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day')) }}'
                        type="date" required name="fecha" id="fecha">
                </div>
                <div class="my-3">
                    <label for="">Nombre del reporte:</label>
                    <input class="form-control" type="text" name="name">
                </div>

                <div class="my-3">
                    <label for="">Canales</label>
                    <div class="form-check">
                        <input class="form-check-input" name='ch[]' type="checkbox" value="todos" id="todos">
                        <label class="form-check-label" for="flexCheckDefault">
                            Seleccionar todos
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input ch" name='ch[]' type="checkbox" value="BOT" id="BOT">
                        <label class="form-check-label" for="flexCheckDefault">
                            BOT
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input ch" name='ch[]' type="checkbox" value="AGENTE" id="AGENTE">
                        <label class="form-check-label" for="flexCheckDefault">
                            AGENTE
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input ch" name='ch[]' type="checkbox" value="EM" id="EM">
                        <label class="form-check-label" for="flexCheckDefault">
                            EMAIL
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input ch" name='ch[]' type="checkbox" value="AU" id="AU">
                        <label class="form-check-label" for="flexCheckDefault">
                            IVR
                        </label>
                    </div>
                </div>

                <div class="my-3">
                    <button class="btn btn-success btn-sm" type="submit">Generar</button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        var todos = document.querySelector('#todos')


        function validarFormulario() {
            var fecha = document.getElementById('fecha').value;

            if (fecha === '') {
                alert('Por favor, ingresa una fecha.');
                return false; // Evitar que el formulario se envíe
            }
            if (!check.disabled) {
                if (!todos.checked) {
                    alert('El checkbox "todos" no está seleccionado.');
                    return false;
                    // Puedes realizar acciones adicionales si el checkbox está seleccionado
                }
            }

            return true;
        }



        var checks = document.querySelectorAll('.ch')
        checks.forEach(function(check) {
            check.addEventListener('click', function(event) {
                todos.disabled = !todos.disabled;
            });
        });


        todos.addEventListener('click', function(event) {
            checks.forEach(function(check) {
                check.disabled = !check.disabled;
            });
        });
    </script>
@endsection
