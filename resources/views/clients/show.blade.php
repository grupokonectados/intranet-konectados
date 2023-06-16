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



    @foreach ($dataEstrategias as $data)
        <div class="col-12 mb-3">
            <div class="card bg-white">
                <div class="card-header">
                    {{ $data->query_description }}
                </div>
                <div class="card-body bg-white">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Canal: </label>
                            <label class="form-label">
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
                                </label>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Consulta: {{ $data->showQue }}</label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endsection
