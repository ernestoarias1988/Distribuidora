<?php

use App\Venta;
use Illuminate\Http\Request;

$cliente = $data['cliente'];
$direccion = $data['direccion'];
$remitente = "Distribuidora";
$vendedor = $data['vendedor'];
$mensajePie = "Gracias por su compra!";
$numero = $data['facturaNro'];
$descuento = $data['descuento'];
$fecha = date("Y-m-d");
$subtotal = 0;
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./bs3.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pedido</title>
    <style>
        body {
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div>
        <?php
        $request = $data['Request'];
        $venta = Venta::findOrFail($request->get("id"));
        foreach ($venta->productos as $producto) {
            $totalProducto = $producto->cantidad * $producto->precio;
            $subtotal += $totalProducto;
        }
        ?>
    </div>
    <table>
        <thead>
            <tr>
                <th colspan="2">Cliente: {{$venta->cliente->nombre}}</th>
                <th colspan="2" class="text-right">Fecha: <?php echo date("d/m/Y"); ?></th>
            </tr>
            <tr>
                <th colspan="2">
                    <strong>Presupuesto</strong><br>
                    {{$venta->cliente->direccion}}<br>
                    Localidad: {{$venta->cliente->localidad}}<br>
                    Vendedor: {{$venta->vendedor}}
                </th>
                <th colspan="2" class="text-right">
                    <img style="width:80px;" src="{{url('/img/logo.png')}}">
                </th>
            </tr>
            <tr>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 50%;">Descripción</th>
                <th class="text-right" style="width: 20%;">Precio unitario</th>
                <th class="text-right" style="width: 20%;">SubTotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->productos as $producto)
            <tr>
                <td class="text-center">{{$producto->cantidad}} U.</td>
                <td>{{$producto->descripcion}}</td>
                <td class="text-right">${{number_format($producto->precio, 2)}}</td>
                <td class="text-right">${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
            </tr>
            <?php $total += ($producto->cantidad * $producto->precio); ?>
            @endforeach
            <tr>
                <td colspan="3" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>${{number_format($total, 2)}}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>