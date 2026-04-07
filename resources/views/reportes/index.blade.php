@extends("maestra")
@section("titulo", "Reportes")

@section("contenido")
<style>
    .report-header {
        background: linear-gradient(135deg, #0f4c75 0%, #3282b8 100%);
        border-radius: 12px;
        color: #fff;
        padding: 1.5rem;
        box-shadow: 0 8px 24px rgba(15, 76, 117, 0.25);
    }
    .report-header h1 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.4px;
    }
    .report-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
    }
    .report-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eef1f4;
        font-weight: 600;
    }
    .table thead th {
        border-top: 0;
        font-size: 0.9rem;
        color: #495057;
    }
    .badge-title {
        font-size: 0.8rem;
        padding: 0.35rem 0.5rem;
        border-radius: 6px;
    }
    .chart-wrap {
        min-height: 320px;
        position: relative;
    }
    .chart-wrap canvas {
        width: 100% !important;
        height: 320px !important;
    }
</style>

<div class="row mb-3">
    <div class="col-12">
        <div class="report-header">
            <h1>Reportes <i class="fa fa-chart-line"></i></h1>
            <p class="mb-0 mt-2">Seccion exclusiva para administradores. Selecciona el tipo de reporte y genera los resultados.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        @include("notificacion")
        <div class="card report-card">
            <div class="card-body">
                <form method="GET" action="{{ route('reportes.index') }}" class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="tipo_reporte"><strong>Tipo de reporte</strong></label>
                        <select class="form-control" id="tipo_reporte" name="tipo_reporte" required>
                            <option value="" disabled {{ !$tipoReporte ? 'selected' : '' }}>Seleccionar...</option>
                            @foreach($tiposReporte as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ $tipoReporte === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3 js-filtro-productos">
                        <label for="vendedor"><strong>Vendedor</strong></label>
                        <select class="form-control" id="vendedor" name="vendedor">
                            <option value="">Todos</option>
                            @foreach($vendedores as $itemVendedor)
                                <option value="{{ $itemVendedor }}" {{ $vendedorSeleccionado === $itemVendedor ? 'selected' : '' }}>{{ $itemVendedor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3 js-filtro-productos">
                        <label for="localidad"><strong>Localidad</strong></label>
                        <select class="form-control" id="localidad" name="localidad">
                            <option value="">Todas</option>
                            @foreach($localidades as $itemLocalidad)
                                <option value="{{ $itemLocalidad }}" {{ $localidadSeleccionada === $itemLocalidad ? 'selected' : '' }}>{{ $itemLocalidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3 js-filtro-cantidad" style="display: none;">
                        <label for="agrupar_por"><strong>Agrupar por</strong></label>
                        <select class="form-control" id="agrupar_por" name="agrupar_por">
                            <option value="vendedor" {{ $agruparPor === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="localidad" {{ $agruparPor === 'localidad' ? 'selected' : '' }}>Localidad</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2 js-filtro-cantidad" style="display: none;">
                        <label for="top_n"><strong>Top N</strong></label>
                        <input
                            type="number"
                            class="form-control"
                            id="top_n"
                            name="top_n"
                            min="3"
                            max="20"
                            value="{{ $topN ?? 8 }}"
                        >
                    </div>

                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-cogs"></i> Generar reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($tipoReporte === 'productos_mas_menos')
    @foreach($periodos as $dias)
        @php
            $reporte = $reportes[$dias] ?? null;
            $masVendidos = $reporte["masVendidos"] ?? collect();
            $menosVendidos = $reporte["menosVendidos"] ?? collect();
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">Ultimos {{ $dias }} dias</h4>
                    <span class="badge badge-info badge-title">15 Mas y Menos vendidos</span>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card report-card h-100">
                    <div class="card-header text-success">15 productos mas vendidos</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Codigo</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($masVendidos as $item)
                                        <tr>
                                            <td>{{ $item->descripcion }}</td>
                                            <td>{{ $item->codigo_barras }}</td>
                                            <td>{{ number_format($item->total_vendido, 0, ',', '.') }}</td>
                                            <td>${{ number_format($item->monto_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Sin datos para este periodo</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card report-card h-100">
                    <div class="card-header text-danger">15 productos menos vendidos</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Codigo</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($menosVendidos as $item)
                                        <tr>
                                            <td>{{ $item->descripcion }}</td>
                                            <td>{{ $item->codigo_barras }}</td>
                                            <td>{{ number_format($item->total_vendido, 0, ',', '.') }}</td>
                                            <td>${{ number_format($item->monto_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Sin datos para este periodo</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

@if($tipoReporte === 'cantidad_vendida')
    @foreach($periodosCantidad as $dias)
        @php
            $reporte = $reportesCantidad[$dias] ?? null;
            $datos = $reporte['datos'] ?? collect();
            $totalRegistros = $reporte['total_registros'] ?? 0;
            $tituloAgrupacion = $agruparPor === 'localidad' ? 'Localidad' : 'Vendedor';
            $totalMontoPeriodo = (float) $datos->sum('total_monto');
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="card report-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Ingresos ($) por {{ strtolower($tituloAgrupacion) }} - Ultimos {{ $dias }} dias</span>
                        <span class="badge badge-info badge-title">Top {{ $topN ?? 8 }} + Otros</span>
                    </div>
                    <div class="card-body">
                        @if($datos->count() > 0)
                            @if($totalRegistros > ($topN ?? 8))
                                <p class="text-muted small mb-3">
                                    Se muestran los {{ $topN ?? 8 }} con mayor monto vendido y el resto se agrupa como <strong>Otros</strong>.
                                </p>
                            @endif
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="chart-wrap">
                                        <canvas id="pieChart{{ $dias }}"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>{{ $tituloAgrupacion }}</th>
                                                    <th>Monto vendido</th>
                                                    <th>% del total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($datos as $fila)
                                                    @php
                                                        $porcentaje = $totalMontoPeriodo > 0
                                                            ? ((float) $fila->total_monto / $totalMontoPeriodo) * 100
                                                            : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $fila->etiqueta }}</td>
                                                        <td>${{ number_format($fila->total_monto, 2, ',', '.') }}</td>
                                                        <td>{{ number_format($porcentaje, 2, ',', '.') }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Sin datos para este periodo.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@if($tipoReporte === 'cantidad_vendida')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endif

<script>
    window.addEventListener('load', function() {
        var tipoReporte = document.getElementById('tipo_reporte');
        var filtrosProductos = document.querySelectorAll('.js-filtro-productos');
        var filtrosCantidad = document.querySelectorAll('.js-filtro-cantidad');

        function toggleFiltros() {
            var esCantidad = tipoReporte && tipoReporte.value === 'cantidad_vendida';

            filtrosProductos.forEach(function(item) {
                item.style.display = esCantidad ? 'none' : '';
            });

            filtrosCantidad.forEach(function(item) {
                item.style.display = esCantidad ? '' : 'none';
            });
        }

        if (tipoReporte) {
            tipoReporte.addEventListener('change', toggleFiltros);
            toggleFiltros();
        }

        @if($tipoReporte === 'cantidad_vendida')
            if (typeof Chart === 'undefined') {
                return;
            }

            var coloresBase = [
                '#1b998b', '#2d3047', '#ff9b71', '#e84855', '#f9dc5c',
                '#43aa8b', '#577590', '#f3722c', '#f8961e', '#277da1',
                '#4d908e', '#90be6d', '#f94144', '#f9844a', '#264653'
            ];

            @foreach($periodosCantidad as $dias)
                @php
                    $datos = ($reportesCantidad[$dias]['datos'] ?? collect())->values();
                @endphp
                @if($datos->count() > 0)
                    var canvas{{ $dias }} = document.getElementById('pieChart{{ $dias }}');
                    if (canvas{{ $dias }}) {
                        var etiquetas{{ $dias }} = {!! json_encode($datos->pluck('etiqueta')->toArray()) !!};
                        var valores{{ $dias }} = {!! json_encode($datos->pluck('total_monto')->map(function ($item) { return (float) $item; })->toArray()) !!};
                        var colores{{ $dias }} = etiquetas{{ $dias }}.map(function(_, i) {
                            return coloresBase[i % coloresBase.length];
                        });

                        new Chart(canvas{{ $dias }}.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: etiquetas{{ $dias }},
                                datasets: [{
                                    data: valores{{ $dias }},
                                    backgroundColor: colores{{ $dias }},
                                    borderColor: '#ffffff',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var data = context.dataset.data || [];
                                                var total = data.reduce(function(sum, val) {
                                                    return sum + Number(val || 0);
                                                }, 0);
                                                var current = Number(context.raw || 0);
                                                var porcentaje = total > 0 ? ((current / total) * 100) : 0;

                                                return context.label + ': $' + current.toLocaleString('es-AR', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                }) + ' (' + porcentaje.toLocaleString('es-AR', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                }) + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                @endif
            @endforeach
        @endif
    });
</script>
@endsection
