@extends('adminlte::page')

@section('title', 'Dashboard Administración')

@section('content_header')
<h1>Cajas</h1>
@stop

@section('content')


<div class="container">

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card bg-info">
                <div class="card-header">
                    <h5 class="card-title">Cajas Activadas</h5>
                </div>
                <div class="card-body">
                    <h1>{{ $cajas_activadas }}</h1>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-danger">
                <div class="card-header">
                    <h5 class="card-title">Cajas Desactivadas</h5>
                </div>
                <div class="card-body">
                    <h1>{{ $cajas_desactivadas }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="container mt-1">




    <h2>Lista de Cajas</h2>

    <a class="btn btn-primary mb-2" href="{{ route('cajas.create') }}">Crear Caja</a>
    <a class="btn btn-secondary mb-2" href="{{ route('cajas.log') }}">Ver registro de conexiones</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>MAC</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($cajas as $caja)
            <tr>
                <td>{{ $caja->id }}</td>
                <td>{{ $caja->mac }}</td>
                <td>{{ $caja->nombre }}</td>
                <td>
                    @if ($caja->estado == 'activado')
                    <span class="badge badge-success">Activado</span>
                    @else
                    <span class="badge badge-danger">Desactivado</span>
                    @endif
                </td>

                <td>
                    <a class="btn btn-warning" href="{{ route('cajas.edit', $caja->id) }}">Editar</a>

                    <a class="btn btn-info" href="{{ route('admin.paquetes.cajas.edit', $caja->id) }}">Paquetes</a>
                    <form action="{{ route('cajas.destroy', $caja->id) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta caja?');" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>


            </tr>
            @endforeach
        </tbody>
    </table>
</div>@stop
