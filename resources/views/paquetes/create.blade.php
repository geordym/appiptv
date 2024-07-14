@extends('adminlte::page')

@section('title', 'Dashboard Administración')

@section('content_header')
<h1>Paquetes</h1>
@stop

@section('content')

<div class="container mt-1">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <h2>Crear Paquete</h2>
    <form action="{{ route('admin.paquetes.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <button type="submit" class="btn btn-primary">Crear</button>
    </form>



</div>


@stop
