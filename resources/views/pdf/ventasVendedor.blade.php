<?php

use App\Venta;
use Illuminate\Http\Request;

//["ventas" => Venta::all()];
//$localidad = $data['localidad'];
$ventas = $data['ventas'];
$vendedor = $data['vendedor'];
$localidad = $data['localidad'];
$total = 0;
$totalACum = 0;
$duplicados = [1,2];
/*
$cliente = $data['cliente'];
$direccion = $data['direccion'];
$remitente = "Distribuidora";
$vendedor = $data['vendedor'];
$mensajePie = "Gracias por su compra!";
$numero = $data['facturaNro'];
$descuento = $data['descuento'];
//$porcentajeImpuestos = 16;
$fecha = date("Y-m-d");
*/
?>
<?php
if (!function_exists('splitProductosColumns')) {
    function splitProductosColumns($productos, $page, $perPage = 40, $perCol = 20) {
        $all = $productos->all();
        $start = $page * $perPage;
        $pageSlice = array_slice($all, $start, $perPage);
        $left = array_slice($pageSlice, 0, $perCol);
        $right = array_slice($pageSlice, $perCol, $perCol);
        return [$left, $right];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./bs3.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pedido</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 ">
                <h1></h1>
            </div>
            @if($ventas->isEmpty())
                <div style="margin: 30px; font-size: 1.2em; color: #b00;">
                    No hay ventas para este vendedor y localidad.
                </div>
            @else
                @foreach($ventas->sortBy('created_at') as $venta)
@if($venta->cliente && $venta->productos && $venta->productos->count() > 0)
    @php
        $total = 0;
        $productos = $venta->productos->all();
        foreach($productos as $producto) {
            $total += ($producto->cantidad * $producto->precio);
        }
        $rowsPerTable = 25;
        $numPages = ceil(count($productos) / $rowsPerTable);
    @endphp
    @for($page = 0; $page < $numPages; $page++)
        @php
            $slice = array_slice($productos, $page * $rowsPerTable, $rowsPerTable);
        @endphp
        <table width="100%" style="table-layout: fixed; page-break-inside: avoid; border-spacing: 0;">
            <tr>
                @for($col = 0; $col < 2; $col++)
                    <td style="vertical-align: top; width: 50%; padding: 0 8px; box-sizing: border-box;">
                        <h3 style="text-align: center; margin:2px">Distribuidora Dany</h3>
                        <table style="width:100%; border-collapse: collapse; font-size:80%;">
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: left; border: 1px solid #000;">
                                        Cliente: {{$venta->cliente->nombre}}<br>
                                        Dirección: {{$venta->cliente->direccion}}<br>
                                        Localidad: {{$venta->cliente->localidad}}<br>
                                        Vendedor: {{$venta->vendedor}}<br>
                                        Fecha: {{ date("d/m/Y") }}<img style="width:80px; position:relative; bottom:60px; float:right;" src="{{ url('/img/logo.png') }}">
                                    </th>
                                </tr>
                                <tr style="border: 1px solid #000; text-align: left; font-weight:10">
                                    <th style="border: 1px solid #000; width: 3%">Cantidad</th>
                                    <th style="text-align:left; border: 1px solid #000; width: 60%">Descripción</th>
                                    <th style="border: 1px solid #000; width: 22%">Precio unitario</th>
                                    <th style="border: 1px solid #000; width: 15%">SubTotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slice as $producto)
                                    <tr>
                                        <td style="border: 1px solid #000;">{{$producto->cantidad}} U.</td>
                                        <td style="text-align:left; border: 1px solid #000;">{{$producto->descripcion}}</td>
                                        <td style="border: 1px solid #000;">${{number_format($producto->precio, 2)}}</td>
                                        <td style="border: 1px solid #000;">${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
                                    </tr>
                                @endforeach
                                @if($page == $numPages-1)
                                    <tr>
                                        <td colspan="3" style="text-align:center; border: 1px solid #000;font-size: 15px; font-weight:bold">Total</td>
                                        <td style="border: 1px solid #000; font-weight:bold">${{number_format($total, 2)}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                @endfor
            </tr>
        </table>
        @if($page < $numPages - 1)
            <div style="page-break-after: always;"></div>
        @endif
    @endfor
@endif
<div style="page-break-after: always;"></div>
@endforeach
@endif
        </div>
    </div>
</body>
</html>