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
    .productos-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 1rem;
    }
    .productos-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 1rem;
    }
    .summary-card {
        border: 1px solid #dbe4ea;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff, #f7fafc);
        padding: 0.85rem 1rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .summary-card small {
        display: block;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.35rem;
    }
    .summary-card strong {
        color: #0f172a;
        font-size: 1.35rem;
    }
    .categoria-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        background: #e8f3ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .categoria-filter {
        min-width: 220px;
    }
    .import-help {
        margin-top: 0.75rem;
        padding: 0.75rem 0.9rem;
        border: 1px dashed #bfdbfe;
        border-radius: 12px;
        background: #f8fbff;
        color: #334155;
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

@php
    $resumenCategorias = $productos
        ->groupBy(function ($producto) {
            return $producto->categoria ?: 'General';
        })
        ->map(function ($items) {
            return $items->count();
        })
        ->sortDesc();
@endphp

<div class="row">
    <div class="col-12">
        <h1>Productos <i class="fa fa-box"></i></h1>
        <div class="productos-toolbar">
            <div>
                <a href="{{route("productos.create")}}" class="btn btn-success btn-sm mb-2">Agregar</a>
                <a href="{{route("categorias.index")}}" class="btn btn-outline-secondary btn-sm mb-2">Categorias</a>
                <button style="text-align:center" class="btn btn-primary btn-sm mb-2" onClick="window.print()">Imprimir Productos</button>
                <button style="text-align:center" class="btn btn-success btn-sm mb-2" onClick="window.location.href='{{ url('/exportarp') }}'">Exportar a Excel</button>
            </div>
            <div class="categoria-filter">
                <label for="filtroCategoria" class="mb-1"><strong>Filtrar por categoría</strong></label>
                <select id="filtroCategoria" class="form-control form-control-sm">
                    <option value="">Todas las categorías</option>
                    @foreach($resumenCategorias as $categoria => $cantidad)
                    <option value="{{$categoria}}">{{$categoria}} ({{$cantidad}})</option>
                    @endforeach
                </select>
            </div>
        </div>
        @include("notificacion")
        <div class="productos-summary">
            <div class="summary-card">
                <small>Total de productos</small>
                <strong>{{$productos->count()}}</strong>
            </div>
            <div class="summary-card">
                <small>Categorías activas</small>
                <strong>{{$resumenCategorias->count()}}</strong>
            </div>
            <div class="summary-card">
                <small>Categoría principal</small>
                <strong>{{$resumenCategorias->keys()->first() ?? 'Sin datos'}}</strong>
            </div>
        </div>
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
                        <th>Categoría</th>
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
                        <td><span class="categoria-badge">{{$producto->categoria ?: 'General'}}</span></td>
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
        var tablaProductos = $('#productosTable').DataTable({
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

        $('#filtroCategoria').on('change', function() {
            var value = $.fn.dataTable.util.escapeRegex($(this).val());
            tablaProductos
                .column(2)
                .search(value ? '^' + value + '$' : '', true, false)
                .draw();
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