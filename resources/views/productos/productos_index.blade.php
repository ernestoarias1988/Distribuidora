@extends("maestra")
@section("titulo", "Productos")
@section("contenido")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    body {
        font-size: 0.9rem;
    }
    .table-highlight tbody tr {
        transition: background-color 0.3s;
    }
    .table-highlight tbody tr:hover {
        background-color: #f1f1f1;
    }
    .table th, .table td {
        padding: 0.3rem;
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="row">
    <div class="col-12">
        <h1>Productos <i class="fa fa-box"></i></h1>
        <a href="{{route("productos.create")}}" class="btn btn-success btn-sm mb-2">Agregar</a>
        @include("notificacion")
        <button style="text-align:center" class="btn btn-primary btn-sm mb-2" onClick="window.print()">Imprimir Productos</button>
        <button style="text-align:center" class="btn btn-success btn-sm mb-2" onClick="window.location.href='https://distribuidoradaxs.com/public/exportarp'">Exportar a Excel</button>
        <div class="card-body">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="custom-file" style="width: fit-content; margin-bottom:0.7%">
                    <input type="file" name="file" class="custom-file-input" id="customFile">
                    <label class="custom-file-label" for="customFile">Seleccionar archivo</label>
                </div>
                <button class="btn btn-success btn-sm" id="importButton" disabled>Importar Productos</button>
            </form>
        </div>
        <div class="table-responsive">
            <table id="productosTable" class="table table-bordered table-striped table-highlight">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Precio Lista 1</th>
                        <th>Precio Lista 2</th>
                        <th>Precio Lista 3</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td>{{$producto->codigo_barras}}</td>
                        <td>{{$producto->descripcion}}</td>
                        <td>${{number_format($producto->precio_venta1, 2)}}</td>
                        <td>${{number_format($producto->precio_venta2, 2)}}</td>
                        <td>${{number_format($producto->precio_venta3, 2)}}</td>
                        <td>{{$producto->existencia}}</td>
                        <td>
                            <a href="{{route("productos.edit", $producto)}}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                            <form action="{{route("productos.destroy", $producto)}}" method="post" style="display:inline-block;">
                                @method("delete")
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#productosTable').DataTable({
            "order": [[0, "asc"]], // Sort by the 1st column (Código) in ascending order
            "language": {
                "lengthMenu": "Mostrar _MENU_ productos",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 productos",
                "infoFiltered": "(filtrado de _MAX_ productos totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

        // Update the label of the file input when a file is selected
        $('.custom-file-input').on('change', function(event) {
            var inputFile = event.currentTarget;
            $(inputFile).parent()
                .find('.custom-file-label')
                .html(inputFile.files[0].name);

            // Enable the import button if a file is selected
            if (inputFile.files.length > 0) {
                $('#importButton').prop('disabled', false);
            } else {
                $('#importButton').prop('disabled', true);
            }
        });
    });
</script>
@endsection