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
        <h1>Paquetes registrados</h1>

    </div>
    <div class="row">
        <a href="{{route('admin.paquetes.create')}}" type="button" class="btn btn-primary">
            Crear Paquetes
        </a>


    </div>
    <div class="row bg-white mt-3" style="max-height: 500px; overflow: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad Canales</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($paquetes as $paquete)





                <tr class="">
                    <td>{{ $paquete->id }}</td>
                    <td>{{ $paquete->nombre }}</td>
                    <td>{{ $paquete->canales_count }}</td>

                    <td>

                        <a class=" btn btn-info" href="{{route('admin.paquetes.edit', $paquete->id)}}">Actualizar</a>

                        <form action="{{ route('admin.paquetes.delete', $paquete->id) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este paquete?');" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>



</div>






<!-- Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Crear Tarifa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.canales.create')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" class="form-control" id="key" name="key" required>
                    </div>
                    <div class="form-group">
                        <label for="email">URL:</label>
                        <input type="text" class="form-control" id="value" name="value" required>
                    </div>
                    <div class="form-group">
                        <label for="rol">Categoria:</label>
                        <input type="text" class="form-control" id="type" name="type" required>

                    </div>

                    <div class="form-group">
                        <label for="rol">Number:</label>
                        <input type="number" class="form-control" id="number" name="number" required>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Canal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

</script>



@stop
