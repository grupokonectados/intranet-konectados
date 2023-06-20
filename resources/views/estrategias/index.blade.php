@extends('layouts.app')

@section('content')
{{-- <div class="col-12 mb-3 ">
    <div class="card bg-white">
        <div class="card-body">
            <a href='{{ route("estrategia.create") }}' class="btn btn-success btn-sm">Crear</a>
        </div>
    </div>
</div>
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <table class="table bg-white table-bordered">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th width="10%">Canal</th>
                            <th width="20%">Descripcion</th>
                            <th width="40%">Consulta</th>
                            <th width="20%">Empresa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $d)
                            <tr onclick="window.location='/estrategia/{{ $d->id }}';">
                                <td class="text-center">
                                    @switch($d->channels)
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
                                    <td class="text-center">{{ $d->query_description }}</td>
                                    <td>{{ $d->onlyWhere }}</td>
                                    <td class="text-center"><a href="{{ route('clients.show',  $d->client_id) }}">{{ $d->name }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}








        <div class="col-12 mb-3 ">
            <div class="card bg-white">
                <div class="card-body">
                    <a href='{{ route("estrategia.create") }}' class="btn btn-success btn-sm">Crear</a>
                </div>
            </div>
        </div>
            <div class="col-6">
                <div class="card bg-white">
                    <div class="card-header">
                        <h5 class="mb-0">Estrategias activas</h5>
                    </div>
                    <div class="card-body bg-white">
                        <table class="table bg-white table-bordered mb-0">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th width="20%">Descripcion</th>
                                    <th width="10%">Canal</th>
                                    <th width="20%">Empresa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $d)
                                @if ($d->isActive == 1)
                                    <tr onclick="window.location='/estrategia/{{ $d['id'] }}';">
                                        <td class="text-center">{{ $d['query_description'] }}</td>
                                        <td class="text-center">
                                            @switch($d['channels'])
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
                                            
                                            <td class="text-center"><a href="{{ route('clients.show',  $d['client_id']) }}">{{ $d['name'] }}</a></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card bg-white">
                        <div class="card-header">
                            <h5 class="mb-0">Estrategias inactivas</h5>
                        </div>
                        <div class="card-body bg-white">
                            <table class="table bg-white table-bordered mb-0">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th width="20%">Descripcion</th>
                                        <th width="10%">Canal</th>
                                        <th width="20%">Empresa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $d)
                                    @if ($d->isActive == 0)
                                        <tr onclick="window.location='/estrategia/{{ $d['id'] }}';">
                                            <td class="text-center">{{ $d['query_description'] }}</td>
                                            <td class="text-center">
                                                @switch($d['channels'])
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
                                                
                                                <td class="text-center"><a href="{{ route('clients.show',  $d['client_id']) }}">{{ $d['name'] }}</a></td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    @endsection
