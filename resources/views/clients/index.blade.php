@extends('layouts.app')

@section('content')
    <div class="col-12 mb-3 ">
        <div class="card bg-white">
            <div class="card-body">
                <a href='#' class="btn btn-success btn-sm">Crear</a>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body bg-white">
                <table class="table table-sm mb-0 table-bordered table-hover">
                    <thead>
                        <tr >
                            <th>Nombre</th>
                            <th>Prefix</th>
                        </tr>
                    </thead>
                    {{-- PARA METODOS LARAVEL --}}
                    {{-- <tbody>
                        @foreach ($data as $d)
                            <tr onclick="window.location='/clients/{{ $d->id }}';">
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->prefix }}</td>
                            </tr>
                        @endforeach
                    </tbody> --}}



                    {{-- PARA METODOS API --}}
                    <tbody>
                        @foreach ($data as $d)
                            <tr style="cursor: pointer" onclick="window.location='/clients/{{ $d['id'] }}';">
                                <td>{{ $d['name'] }}</td>
                                <td>{{ $d['prefix'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
