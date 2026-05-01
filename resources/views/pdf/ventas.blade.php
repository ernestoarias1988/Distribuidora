<?php

$ventas = $data['ventas'];
$localidad = $data['localidad'];
$periodo = $data['periodo'] ?? 'mes';
$logoPath = public_path('img/logo.png');
$logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
$itemsPorHoja = 23;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets de ventas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            line-height: 1.1;
            color: #111827;
        }
        .ticket-pair {
            width: 100%;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .ticket-pair.page-break {
            page-break-after: always;
        }
        .ticket-pair-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .ticket-pair-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .ticket-pair-table td:first-child {
            padding-right: 4px;
        }
        .ticket-pair-table td:last-child {
            padding-left: 4px;
        }
        .ticket-copy {
            border: 1px solid #111827;
            padding: 5px;
            box-sizing: border-box;
            position: relative;
        }
        .ticket-header {
            margin-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 3px;
        }
        .ticket-header h2 {
            margin: 0 0 3px;
            text-align: left;
            font-size: 9.5px;
        }
        .ticket-brand {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
        }
        .ticket-brand-subtitle {
            margin: 1px 0 0;
            color: #4b5563;
            font-size: 7px;
        }
        .ticket-header-top {
            width: 100%;
            margin-bottom: 2px;
            padding-right: 96px;
            border-collapse: collapse;
        }
        .ticket-logo-floating {
            position: absolute;
            top: 5px;
            right: 5px;
            line-height: 0;
        }
        .ticket-logo {
            width: 80px;
            height: auto;
        }
        .ticket-meta {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-meta td {
            padding: 1px 0;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #111827;
            padding: 2px 3px;
            font-size: 8px;
        }
        .items-table th {
            background: #f3f4f6;
            text-align: left;
            font-size: 8px;
        }
        .items-table td.number,
        .items-table th.number,
        .ticket-total {
            text-align: right;
        }
        .ticket-total {
            margin-top: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .ticket-continued {
            margin-top: 4px;
            font-size: 8px;
            font-weight: bold;
            color: #4b5563;
            text-align: right;
        }
        .item-category {
            margin-top: 1px;
            color: #4b5563;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .empty-state {
            margin-top: 30px;
            text-align: center;
            color: #4b5563;
        }
    </style>
</head>
<body>
    @php $printedVentas = 0; @endphp

    @foreach($ventas as $venta)
        @if(!$venta->cliente || !$venta->productos || $venta->productos->isEmpty())
            @continue
        @endif

        @php
            $printedVentas++;
            $total = $venta->productos->sum(function ($producto) {
                return $producto->cantidad * $producto->precio;
            });
            $bloquesProductos = $venta->productos->chunk($itemsPorHoja);
            $cantidadBloques = $bloquesProductos->count();
        @endphp

        @foreach($bloquesProductos as $indiceBloque => $productosBloque)
            @php
                $esUltimoBloque = $indiceBloque === ($cantidadBloques - 1);
                $hojaActual = $indiceBloque + 1;
            @endphp

            <div class="ticket-pair {{ $esUltimoBloque ? '' : 'page-break' }}">
                <table class="ticket-pair-table">
                    <tr>
                        @for($i = 0; $i < 2; $i++)
                            <td>
                                <div class="ticket-copy">
                                    @if($logoData)
                                        <div class="ticket-logo-floating">
                                            <img src="{{ $logoData }}" alt="Logo" class="ticket-logo">
                                        </div>
                                    @endif
                                    <div class="ticket-header">
                                        <table class="ticket-header-top">
                                            <tr>
                                                <td>
                                                    <p class="ticket-brand">Distribuidora Dany</p>
                                                    <p class="ticket-brand-subtitle">{{ $localidad ?: 'Todas' }} - {{ ucfirst($periodo) }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                        <h2>Presupuesto</h2>
                                        <table class="ticket-meta">
                                            <tr>
                                                <td><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</td>
                                                <td class="number"><strong>Fecha:</strong> {{ date('d/m/Y') }} | <strong>Hoja:</strong> {{ $hojaActual }}/{{ $cantidadBloques }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dirección:</strong> {{ $venta->cliente->direccion }} | <strong>Loc:</strong> {{ $venta->cliente->localidad }}</td>
                                                <td class="number"><strong>Vendedor:</strong> {{ $venta->vendedor }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Cant.</th>
                                                <th style="width: 84%;">Descripción</th>
                                                <th class="number" style="width: 3%;">P. unit.</th>
                                                <th class="number" style="width: 3%;">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($productosBloque as $producto)
                                                <tr>
                                                    <td style="width: 10%;">{{ $producto->cantidad }} U.</td>
                                                    <td style="width: 84%;">
                                                        {{ $producto->descripcion }}
                                                        <div class="item-category">Categoria: {{ $producto->categoria ?? 'General' }}</div>
                                                    </td>
                                                    <td class="number" style="width: 3%;">${{ number_format($producto->precio, 2) }}</td>
                                                    <td class="number" style="width: 3%;">${{ number_format($producto->cantidad * $producto->precio, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @if($esUltimoBloque)
                                        <div class="ticket-total">Total: ${{ number_format($total, 2) }}</div>
                                    @else
                                        <div class="ticket-continued">Continúa en la siguiente hoja...</div>
                                    @endif
                                </div>
                            </td>
                        @endfor
                    </tr>
                </table>
            </div>
        @endforeach
    @endforeach

    @if($printedVentas === 0)
        <div class="empty-state">
            No hay ventas para imprimir con los filtros actuales.
        </div>
    @endif
</body>
</html>