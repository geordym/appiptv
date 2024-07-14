@extends('adminlte::page')

@section('title', 'Dashboard Administración')

@section('content_header')
@stop

@section('content')


@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif


<div class="container mt-3">

    <div class="row">
        <h1>Paquetes de La Caja</h1>

    </div>

    <div class="row bg-white mt-3">
        <h2>Paquetes de la caja: <strong>{{$caja->nombre}}</strong></h2>
        <table class="table table-striped">
            <thead>
            <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad Canales</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($caja_paquetes as $paquete)
                <tr class="">
                    <td>{{ $paquete->id }}</td>
                    <td>{{ $paquete->nombre }}</td>
                    <td>{{ $paquete->canales_count }}</td>

                    <td>


                        <form action="{{ route('admin.paquetes.cajas.dettach')}}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este paquete?');" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="paquete" id="paquete" value="{{$paquete->id}}">
                            <input type="hidden" name="caja" id="caja" value="{{$caja->id}}">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row bg-white mt-3">

        <form action="{{route('admin.paquetes.cajas.attach')}}" method="POST">
            @csrf
            <div class="form-group">
                <input type="hidden" value="{{$caja->id}}" name="caja" id="caja">
                <label for="estado">Añadir Paquete:</label>
                <select class="form-control" id="paquete" name="paquete" required>
                    @foreach($paquetes as $paquete)
                    <option value="{{ $paquete->id }}">{{ $paquete->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary mt-3">Añadir Paquete</button>

            </div>

        </form>



    </div>
</div>





@stop
