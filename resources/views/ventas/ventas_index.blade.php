@extends("maestra")
@section("titulo", "Ventas")
@section("contenido")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .table-highlight tbody tr.pagado-0 {
        background-color: #f8d7da; /* Light red */
    }
    .table-highlight tbody tr.pagado-total {
        background-color: #d4edda; /* Light green */
    }
    .table-highlight tbody tr.pagado-partial {
        background-color: #fff3cd; /* Light yellow */
    }
    .form-inline .form-group {
        margin-bottom: 1rem;
    }
</style>

<div class="row">
    <div class="col-12">
        <h1>Ventas <i class="fa fa-list"></i></h1>
        @include("notificacion")
        <a class="btn btn-primary" target="blank" style="margin-top:-0.5%" href="{{route("ventas.pdf", ["id"=>$localidad])}}">
            <i class="fa fa-print"></i>&nbsp; Imprimir tickets por Localidad
        </a>
        <button style="text-align:center" class="btn btn-success mb-2" onClick="window.location.href='https://distribuidoradaxs.com/public/exportarv'">Exportar a Excel</button>
        @if (Auth::user()->role_id=="Administrador")
        <a style="margin-left:0.2%" href="{{route("ventas.acumulados",["show"=>$entregadosFlag])}}">Ver todos los acumulados</a>
        @endif
        <form action="{{route("guardarLocalidad")}}" method="post" class="mb-4" id="localidadForm">
            {{ csrf_field() }}
            @csrf
            <div class="form-group">
                <label for="id_localidad">Seleccione una localidad</label>
                <select class="form-control select2" name="id_localidad" id="id_localidad" style="width: 300px;" required>
                    <option value="" disabled selected>Seleccione una localidad</option>
                    <option value="Todas" {{ old('id_localidad', $localidad) == 'Todas' ? 'selected' : '' }}>Todas</option>
                    <option value="Salta" {{ old('id_localidad', $localidad) == 'Salta' ? 'selected' : '' }}>Salta</option>
                    <option value="Pichanal" {{ old('id_localidad', $localidad) == 'Pichanal' ? 'selected' : '' }}>Pichanal</option>
                    <option value="Orán" {{ old('id_localidad', $localidad) == 'Orán' ? 'selected' : '' }}>Orán</option>
                    <option value="Irigoyen" {{ old('id_localidad', $localidad) == 'Irigoyen' ? 'selected' : '' }}>Irigoyen</option>
                    <option value="Embarcacion" {{ old('id_localidad', $localidad) == 'Embarcacion' ? 'selected' : '' }}>Embarcacion</option>
                    <option value="Isla de Cana" {{ old('id_localidad', $localidad) == 'Isla de Cana' ? 'selected' : '' }}>Isla de Cana</option>
                    <option value="Chaco" {{ old('id_localidad', $localidad) == 'Chaco' ? 'selected' : '' }}>Chaco</option>
                    <option value="Morillo" {{ old('id_localidad', $localidad) == 'Morillo' ? 'selected' : '' }}>Morillo</option>
                    <option value="Colonia Santa Rosa" {{ old('id_localidad', $localidad) == 'Colonia Santa Rosa' ? 'selected' : '' }}>Colonia Santa Rosa</option>
                    <option value="Lozano" {{ old('id_localidad', $localidad) == 'Lozano' ? 'selected' : '' }}>Lozano</option>
                    <option value="Otra" {{ old('id_localidad', $localidad) == 'Otra' ? 'selected' : '' }}>Otra</option>
                </select>
            </div>
        </form>
    </div>
</div>

@if(session("localidad") !== null && session("localidad") != "Todas")
<h4>Localidad: {{$localidad}} <a style="margin-left:0.2%" href="{{route("ventas.indexShowTodos",["show"=>$entregadosFlag])}}">Mostrar todas las localidades</a></h4>
@endif

<div class="row" style="margin: 0.2%; margin-bottom: 1rem;">
    <div class="col-12">
        <form class="form-inline">
            <label for="entregadosDropdown" class="mr-2">Mostrando Entregados:</label>
            <select class="form-control select2" id="entregadosDropdown" style="width: 67px;">
                <option value="0" {{ $entregadosFlag == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ $entregadosFlag == 1 ? 'selected' : '' }}>Sí</option>
            </select>
        </form>
    </div>
</div>

<div style="text-align:center" class="table-responsive">
    <table id="ventasTable" class="table table-bordered table-striped table-highlight">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Localidad</th>
                <th>Total</th>
                <th style="width: 150px;">Pagado</th>
                <th>Diferencia</th>
                <th>Entregado</th>
                <th>Vendedor</th>
                <th>Detalles</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas->sortByDesc('created_at') as $venta)
            @if ((Auth::user()->role_id=="Administrador"||Auth::user()->email==$venta->vendedor) && $venta->created_at > '2023-10-16 11:15:35')
            @if(($venta->cliente->localidad==$localidad || $localidad==='Todas' || $localidad==null) && ($venta->entregado != 1 || $entregadosFlag == 1))
            <tr class="{{ $venta->pagado == 0 ? 'pagado-0' : ($venta->pagado == $venta->total ? 'pagado-total' : 'pagado-partial') }}">
                <td>{{$venta->created_at}}</td>
                <td>{{$venta->cliente->nombre}}</td>
                <td>{{$venta->cliente->localidad}}</td>
                <td>${{number_format($venta->total,2)}}</td>
                <td>
                    <form action="{{route('cargaPago', ['id'=>$venta->id])}}" method="post">
                        {{ csrf_field() }}
                        @csrf
                        <input type="number" step="0.1" required value="{{$venta->pagado}}" class="form-control" name="pago" id="pago" placeholder="">
                    </form>
                </td>
                <td>${{number_format($venta->total-$venta->pagado,2)}}</td>
                <td>
                    @if ($venta->entregado == 0)
                    <a class="btn btn-danger" href="{{route('cargaEntrega', ["id"=>$venta->id])}}">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </a>
                    @else
                    <a class="btn btn-success" href="{{route('cancelEntrega', ["id"=>$venta->id])}}">
                        <i class="fa fa-check-square" aria-hidden="true"></i>
                    </a>
                    @endif
                </td>
                <td><a href="{{url('totales?vendedor=' . $venta->vendedor . '&localidad=' . $localidad)}}"> {{$venta->vendedor}}</a></td>
                <td>
                    <a class="btn btn-success" href="{{route("ventas.show", $venta)}}">
                        <i class="fa fa-info"></i>
                    </a>
                </td>
                <td>
                    <form action="{{route("ventas.destroy", [$venta])}}" method="post" class="delete-form" data-cliente="{{$venta->cliente->nombre}}">
                        @method("delete")
                        @csrf
                        <button type="submit" class="btn btn-danger delete-button">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endif
            @endif
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Seleccione una localidad',
            allowClear: true
        });

        $('#id_localidad').on('change', function() {
            $('#localidadForm').submit();
        });

        $('#entregadosDropdown').on('change', function() {
            var entregadosFlag = $(this).val();
            if (entregadosFlag == 1) {
                window.location.href = "{{route('ventas.indexSiShowEntregados', ['localidad' => $localidad])}}";
            } else {
                window.location.href = "{{route('ventas.indexNoShowEntregados', ['localidad' => $localidad])}}";
            }
        });

        $('#ventasTable').DataTable({
            "pageLength": 50, // Set default number of rows to display
            "order": [[0, "desc"]], // Sort by the 1st column (Fecha) in descending order
            "language": {
                "lengthMenu": "Mostrar _MENU_ ventas",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ ventas",
                "infoEmpty": "Mostrando 0 a 0 de 0 ventas",
                "infoFiltered": "(filtrado de _MAX_ ventas totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

        let debounceTimer;

        $('#id_localidad').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                var query = $('#id_localidad').val();
                if (query != '') {
                    var _token2 = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: "{{ route('autocomplete.fetchlocalidad') }}",
                        method: "POST",
                        data: {
                            query: query,
                            _token: _token2
                        },
                        success: function(data) {
                            $('#localidadlist').fadeIn();
                            $('#localidadlist').html(data);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error fetching data:', textStatus, errorThrown);
                        }
                    });
                }
            }, 300); // Adjust the debounce delay as needed
        });

        $('#localidadlist').on('click', 'li', function() {
            $('#id_localidad').val($(this).text());
            $('#localidadlist').fadeOut();
        });

        // Add confirmation dialog for delete buttons
        $('.delete-form').on('submit', function(e) {
            var form = this;
            e.preventDefault();
            var cliente = $(this).data('cliente');
            var confirmed = confirm('¿Estás seguro de que deseas eliminar esta venta?\nCliente: ' + cliente);
            if (confirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection