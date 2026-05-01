@extends("maestra")
@section("titulo", "Clientes")
@section("contenido")
<div class="row">
    <div class="col-12">
        <h1>Clientes <i class="fa fa-users"></i></h1>
        <a href="{{route("clientes.create")}}" class="btn btn-success mb-2">Agregar</a>
        @if (Auth::user()->role_id=="Administrador")
        <form method="GET" action="{{route('clientes.index')}}" class="form-inline mb-2">
            <label for="estado" class="mr-2">Filtrar estado</label>
            <select name="estado" id="estado" class="form-control mr-2">
                <option value="todos" {{($estadoFiltro ?? 'todos') === 'todos' ? 'selected' : ''}}>Todos</option>
                <option value="habilitados" {{($estadoFiltro ?? 'todos') === 'habilitados' ? 'selected' : ''}}>Habilitados</option>
                <option value="deshabilitados" {{($estadoFiltro ?? 'todos') === 'deshabilitados' ? 'selected' : ''}}>Deshabilitados</option>
            </select>
            <button type="submit" class="btn btn-primary">Aplicar</button>
        </form>
        @endif
        @include("notificacion")
        @if (Auth::user()->role_id=="Administrador")
        <div class="table-responsive">
            <table id="clientesTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Localidad</th>
                        <th>Lista</th>
                        <th>Estado</th>
                        <th>Vendedor</th>
                        <th>Habilitar/Deshabilitar</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes->sortBy('nombre') as $cliente)

                    <tr>
                        <td>{{$cliente->nombre}}</td>
                        <td>{{$cliente->telefono}}</td>
                        <td>{{$cliente->direccion}}</td>
                        <td>{{$cliente->localidad}}</td>
                        <td>{{$cliente->lista}}</td>
                        <td>
                            @if($cliente->estado)
                                <span class="badge badge-success">Habilitado</span>
                            @else
                                <span class="badge badge-secondary">Deshabilitado</span>
                            @endif
                        </td>
                        <td>{{$cliente->vendedor}}</td>
                        <td>
                            <form action="{{route("clientes.toggleEstado", [$cliente])}}" method="post">
                                @csrf
                                <button type="submit" class="btn {{$cliente->estado ? 'btn-secondary' : 'btn-success'}}">
                                    {{$cliente->estado ? 'Deshabilitar' : 'Habilitar'}}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a class="btn btn-warning" href="{{route("clientes.edit",[$cliente])}}">
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <form action="{{route("clientes.destroy", [$cliente])}}" method="post">
                                @method("delete")
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#clientesTable').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 25,
            "language": {
                "lengthMenu": "Mostrar _MENU_ clientes",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ clientes",
                "infoEmpty": "Mostrando 0 a 0 de 0 clientes",
                "infoFiltered": "(filtrado de _MAX_ clientes totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>
@endsection