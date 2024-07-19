@extends('adminlte::page')

@section('title', 'Dashboard Administración')

@section('content_header')
<h1>Cajas</h1>
@stop

@section('content')





<div class="container mt-1">




    <h2>Lista de Cajas</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>MAC</th>
                <th>Fecha</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($cajas_registro as $caja)

            <tr>
                <td>{{ $caja->mac }}</td>
                <td>{{ $caja->created_at }}</td>

                @if($caja->isOnSystem)
                <td>
                <a href="#" class="badge badge-success">En Sistema</a>
                <a href="#" class="badge badge-success">{{$caja->nombre}}</a>
                </td>

                @else
                <td>
                <a class="btn btn-warning" href="{{ route('cajas.create', ['mac' => $caja->mac]) }}">Crear</a>
                </td>

                @endif

            </tr>
            @endforeach
        </tbody>
    </table>
</div>@stop
