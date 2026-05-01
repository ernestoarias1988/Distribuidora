@extends("maestra")
@section("titulo", "Ventas")
@section("contenido")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .ventas-page {
        display: grid;
        gap: 1.25rem;
    }
    .ventas-toolbar {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
        padding: 1.25rem;
    }
    .ventas-toolbar__top {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }
    .ventas-toolbar__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }
    .ventas-toolbar__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }
    .ventas-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background-color: #f1f5f9;
        color: #334155;
        font-size: 0.92rem;
        font-weight: 600;
    }
    .ventas-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    .ventas-field label,
    .ventas-periods__label,
    .ventas-toggle__copy small,
    .ventas-empty p {
        margin-bottom: 0;
    }
    .ventas-field label,
    .ventas-periods__label {
        display: block;
        margin-bottom: 0.55rem;
        color: #475569;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .ventas-periods__group {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.35rem;
        border-radius: 999px;
        background-color: #eef2f7;
    }
    .ventas-periods__option {
        border: 0;
        border-radius: 999px;
        padding: 0.6rem 1rem;
        background: transparent;
        color: #475569;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .ventas-periods__option:hover,
    .ventas-periods__option:focus {
        color: #0f172a;
        text-decoration: none;
    }
    .ventas-periods__option.is-active {
        background-color: #0f172a;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.18);
    }
    .ventas-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 58px;
        padding: 0.8rem 1rem;
        border: 1px solid #dbe4ee;
        border-radius: 16px;
        background-color: #ffffff;
    }
    .ventas-toggle__copy {
        display: grid;
        gap: 0.2rem;
    }
    .ventas-toggle__copy strong {
        color: #0f172a;
        font-size: 0.95rem;
    }
    .ventas-toggle__copy small {
        color: #64748b;
        font-size: 0.82rem;
    }
    .ventas-switch {
        position: relative;
        display: inline-flex;
        width: 54px;
        height: 30px;
        flex-shrink: 0;
    }
    .ventas-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .ventas-switch__slider {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background-color: #cbd5e1;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .ventas-switch__slider::before {
        content: "";
        position: absolute;
        width: 24px;
        height: 24px;
        left: 3px;
        top: 3px;
        border-radius: 50%;
        background-color: #ffffff;
        box-shadow: 0 6px 12px rgba(15, 23, 42, 0.18);
        transition: transform 0.2s ease;
    }
    .ventas-switch input:checked + .ventas-switch__slider {
        background-color: #16a34a;
    }
    .ventas-switch input:checked + .ventas-switch__slider::before {
        transform: translateX(24px);
    }
    .table-highlight tbody tr.pagado-0 {
        background-color: #f8d7da;
    }
    .table-highlight tbody tr.pagado-total {
        background-color: #d4edda;
    }
    .table-highlight tbody tr.pagado-partial {
        background-color: #fff3cd;
    }
    .ventas-empty {
        padding: 2.5rem 1.5rem;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background-color: #f8fafc;
        color: #64748b;
        text-align: center;
    }
    @media (max-width: 768px) {
        .ventas-toolbar {
            padding: 1rem;
        }
        .ventas-toggle {
            padding: 0.75rem 0.9rem;
        }
        .ventas-periods__group,
        .ventas-toolbar__actions,
        .ventas-toolbar__meta {
            width: 100%;
        }
    }
</style>

<div class="ventas-page">
    <div class="row">
        <div class="col-12">
            <h1>Ventas <i class="fa fa-list"></i></h1>
            @include("notificacion")
            <div class="ventas-toolbar">
                <div class="ventas-toolbar__top">
                    <div class="ventas-toolbar__meta">
                        <span class="ventas-badge"><i class="fa fa-calendar"></i> {{$periodoLabel}}</span>
                        <span class="ventas-badge"><i class="fa fa-map-marker"></i> {{$localidad ?? 'Todas'}}</span>
                        <span class="ventas-badge"><i class="fa fa-shopping-cart"></i> {{ $ventas->count() }} ventas</span>
                    </div>
                    <div class="ventas-toolbar__actions">
                        <a class="btn btn-primary" target="blank" href="{{route("ventas.pdf", ["id" => $localidad, "localidad" => $localidad, "periodo" => $periodo, "entregados" => $entregadosFlag])}}">
                            <i class="fa fa-print"></i>&nbsp; Imprimir tickets
                        </a>
                        <button type="button" class="btn btn-success" onClick="window.location.href='https://distribuidoradaxs.com/public/exportarv'">Exportar a Excel</button>
                        @if (Auth::user()->role_id=="Administrador")
                        <a class="btn btn-outline-secondary" href="{{route("ventas.acumulados",["show"=>$entregadosFlag])}}">Ver acumulados</a>
                        @endif
                    </div>
                </div>

                <div class="ventas-filters">
                    <form action="{{route("guardarLocalidad")}}" method="post" id="localidadForm" class="ventas-field mb-0">
                        {{ csrf_field() }}
                        @csrf
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <input type="hidden" name="entregados" value="{{$entregadosFlag}}">
                        <label for="id_localidad">Localidad</label>
                        <select class="form-control select2" name="id_localidad" id="id_localidad" required>
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
                    </form>

                    <div class="ventas-field ventas-periods">
                        <span class="ventas-periods__label">Período</span>
                        <div class="ventas-periods__group">
                            <a class="ventas-periods__option {{ $periodo === 'semana' ? 'is-active' : '' }}" href="{{ route('ventas.index', ['localidad' => $localidad, 'periodo' => 'semana', 'entregados' => $entregadosFlag]) }}">Semana</a>
                            <a class="ventas-periods__option {{ $periodo === 'mes' ? 'is-active' : '' }}" href="{{ route('ventas.index', ['localidad' => $localidad, 'periodo' => 'mes', 'entregados' => $entregadosFlag]) }}">Mes</a>
                            <a class="ventas-periods__option {{ $periodo === 'anio' ? 'is-active' : '' }}" href="{{ route('ventas.index', ['localidad' => $localidad, 'periodo' => 'anio', 'entregados' => $entregadosFlag]) }}">Año</a>
                        </div>
                    </div>

                    <form action="{{ route('ventas.index') }}" method="get" id="entregadosForm" class="mb-0">
                        <input type="hidden" name="localidad" value="{{$localidad}}">
                        <input type="hidden" name="periodo" value="{{$periodo}}">
                        <div class="ventas-toggle">
                            <div class="ventas-toggle__copy">
                                <strong>Mostrar ventas entregadas</strong>
                                <small>{{ $entregadosFlag == 1 ? 'Incluye ventas entregadas y pendientes' : 'Solo muestra ventas pendientes de entrega' }}</small>
                            </div>
                            <label class="ventas-switch" for="entregadosToggle">
                                <input type="checkbox" id="entregadosToggle" name="entregados" value="1" {{ $entregadosFlag == 1 ? 'checked' : '' }}>
                                <span class="ventas-switch__slider"></span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($localidad && $localidad != "Todas")
    <div>
        <a class="btn btn-link pl-0" href="{{ route('ventas.index', ['localidad' => 'Todas', 'periodo' => $periodo, 'entregados' => $entregadosFlag]) }}">Mostrar todas las localidades</a>
    </div>
    @endif

    <div style="text-align:center" class="table-responsive">
        @if($ventas->isEmpty())
        <div class="ventas-empty">
            <h4>No hay ventas para este filtro</h4>
            <p>Probá con otra localidad o cambiá el período para ampliar los resultados.</p>
        </div>
        @else
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
                @foreach($ventas as $venta)
                <tr class="{{ $venta->pagado == 0 ? 'pagado-0' : ($venta->pagado == $venta->total ? 'pagado-total' : 'pagado-partial') }}">
                    <td>{{$venta->created_at}}</td>
                    <td>{{$venta->cliente->nombre}}</td>
                    <td>{{$venta->cliente->localidad}}</td>
                    <td>${{number_format($venta->total,2)}}</td>
                    <td>
                        <form action="{{route('cargaPago', ['id'=>$venta->id])}}" method="post">
                            {{ csrf_field() }}
                            @csrf
                            <input type="hidden" name="localidad" value="{{$localidad}}">
                            <input type="hidden" name="periodo" value="{{$periodo}}">
                            <input type="hidden" name="entregados" value="{{$entregadosFlag}}">
                            <input type="number" step="0.1" required value="{{$venta->pagado}}" class="form-control" name="pago" id="pago" placeholder="">
                        </form>
                    </td>
                    <td>${{number_format($venta->total-$venta->pagado,2)}}</td>
                    <td>
                        @if ($venta->entregado == 0)
                        <a class="btn btn-danger" href="{{route('cargaEntrega', ["id"=>$venta->id, 'localidad' => $localidad, 'periodo' => $periodo, 'entregados' => $entregadosFlag])}}">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </a>
                        @else
                        <a class="btn btn-success" href="{{route('cancelEntrega', ["id"=>$venta->id, 'localidad' => $localidad, 'periodo' => $periodo, 'entregados' => $entregadosFlag])}}">
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
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Seleccione una localidad',
            allowClear: false,
            width: '100%'
        });

        $('#id_localidad').on('change', function() {
            $('#localidadForm').submit();
        });

        $('#entregadosToggle').on('change', function() {
            $('#entregadosForm').submit();
        });

        if ($('#ventasTable').length) {
            $('#ventasTable').DataTable({
                "pageLength": 50,
                "order": [[0, "desc"]],
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
        }

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