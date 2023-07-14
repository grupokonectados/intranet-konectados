@extends('layouts.app')

@section('title-section', $config_layout['title-section'])
@section('breads', $config_layout['breads'])

@section('btn-create')

    <a href="{{ route($config_layout["btn-create"]) }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus-circle"></i>
        Crear
    </a>

@endsection