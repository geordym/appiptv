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
        <h1>Editar Paquete</h1>

    </div>

    <div class="row bg-white mt-3">
    <h2>Canales del Paquete: <strong>{{$paquete->nombre}}</strong></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Nombre</th>
                    <th>URL</th>
                    <th>Categoria</th>
                    <th>NUMBER</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>
                @foreach($paquete_canales as $canal)
                    <tr>
                    <td>{{ $canal["key"] }}</td>
                    <td>{{ $canal["value"] }}</td>
                    <td>{{ $canal["type"] }}</td>
                    <td>{{ $canal["number"] }}</td>
                        <td>

                        <form action="{{ route('admin.paquetes.canales.destroy', $canal->id) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este canal de este paquete?');" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" value="{{$paquete->id}}" name="paquete" id="paquete">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                        </td>


                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row bg-white mt-3">

    <form action="{{route('admin.paquetes.canalAdd', $paquete->id )}}" method="POST">
    @csrf
    <div class="form-group">

            <label for="estado">Añadir Canal:</label>
            <select class="form-control" id="canal" name="canal" required>
    @foreach($canales as $canal)
        <option value="{{ $canal->id }}">{{ $canal->key }}</option>
    @endforeach
</select>

    <button type="submit" class="btn btn-primary mt-3">Añadir Canal</button>

        </div>

    </form>



    </div>
</div>





@stop
