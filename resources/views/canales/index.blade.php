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
        <h1>Canales disponibles</h1>

    </div>
    <div class="row">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
            Crear Canal
        </button>

        <a class="btn btn-warning ml-4" href="{{route('admin.canales.xml')}}">
            Actualizar archivo de canales
        </a>
    </div>
    <div class="row bg-white mt-3" style="max-height: 500px; overflow: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>URL</th>
                    <th>Categoria</th>
                    <th>NUMBER</th>
                    <th>Estado</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($canales as $canal)

                @php
                $estado = "";
                $background = "";
                if($canal->habilitado == 0){
                $estado = "Deshabilitado";
                $background = "bg-danger";
                }

                elseif ($canal->habilitado == 1){
                $estado = "Habilitado";
                $background = "bg-success";
                } else {


                }




                @endphp

                <tr class="{{$background}}">
                    <td>{{ $canal->key }}</td>
                    <td>{{ $canal->value }}</td>
                    <td>{{ $canal->type }}</td>
                    <td>{{ $canal->number }}</td> <!-- Asumiendo que tienes una relación BelongsTo con el rol -->
                    <td>
                        {{$estado}}
                    </td>

                    <td>

                        <a class=" btn btn-info" href="{{route('admin.canales.edit', $canal->id)}}">Actualizar</a>
                        <a class="btn btn-danger ml-4 deleteLink" href="{{ route('admin.canales.delete', $canal->id) }}">Eliminar</a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>



</div>


<div class="container mt-3">

    <div class="row">
        <h1>Canales que se encuentran actualmente en el IPTV</h1>

    </div>
    <div class="row">

    </div>
    <div class="row bg-white mt-3" style="max-height: 400px; overflow: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>URL</th>
                    <th>Categoria</th>
                    <th>NUMBER</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($canales_instalados as $canal)


                <tr class="bg-info">
                    <td>{{ $canal["key"] }}</td>
                    <td>{{ $canal["value"] }}</td>
                    <td>{{ $canal["type"] }}</td>
                    <td>{{ $canal["number"] }}</td> <!-- Asumiendo que tienes una relación BelongsTo con el rol -->

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
                <h5 class="modal-title" id="createUserModalLabel">Crear Caja</h5>
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
    document.querySelectorAll('.deleteLink').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Evitar el comportamiento predeterminado del enlace

            if (confirm('¿Estás seguro de que deseas eliminar este canal?')) {
                // Enviar una solicitud DELETE al servidor
                fetch(event.target.href, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Agrega el token CSRF
                    }
                }).then(response => {
                    if (response.status === 200) {
                        // Éxito: redirige o muestra un mensaje de éxito
                        window.location.href = "{{ route('admin.canales') }}";
                    } else {
                        // Error: muestra un mensaje de error o maneja la respuesta
                        alert('Error al eliminar el canal.');
                    }
                });
            }
        });
    });
</script>



@stop
