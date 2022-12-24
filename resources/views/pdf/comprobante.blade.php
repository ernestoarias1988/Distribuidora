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
//$porcentajeImpuestos = 16;
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
    <table style="text-align: center; width:100%; border-collapse: collapse; font-size:90%;">

        <thead>
            <tr>
                <th style="text-align: left; border: 1px solid #000;border-right: 1px solid #fff; font-weight:10">Vendedor: {{$venta->vendedor}}</th>
                <th style="border: 1px solid #000; border-right: 1px solid #000;"></th>
                <th style="border: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #fff;"></th>
                <th style="border: 1px solid #000; text-align: rigth;"> Fecha: <?php echo date("d/m/Y"); ?>
                </th>
            </tr>
            <tr>
                <th style="text-align: left; font-weight:10">Cliente: {{$venta->cliente->nombre}}<br>{{$venta->cliente->direccion}}<br>Localidad: {{$venta->cliente->localidad}} </th>
                <th></th>
                <th>
                    <img style="width:80px; " src="{{url("/img/logo.png")}}">
                </th>
                <th>
                </th>
            </tr>
            <tr>
                <th style="text-align: left; font-weight:10">
                </th>
            </tr>
            <tr>
                <th style="text-align: left; font-weight:10">
            </tr>
            <tr style="border: 1px solid #000; text-align: left;  font-weight:10">
                <th style="border: 1px solid #000;">Cantidad</th>
                <th style="text-align:left; border: 1px solid #000;">Descripción</th>
                <th style="border: 1px solid #000;">Precio unitario</th>
                <th style="border: 1px solid #000;">SubTotal</th>
            </tr>
        </thead>
        <tbody style="border: 1px solid #000; ">
            @foreach($venta->productos as $producto)
            <tr>
                <td style="border: 1px solid #000"> {{$producto->cantidad}} U. </td>
                <td style="text-align:left; border: 1px solid #000">{{$producto->descripcion}} </td>
                <td style=" border: 1px solid #000"> ${{number_format($producto->precio, 2)}}</td>
                <td style="border: 1px solid #000"> ${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
            </tr>
            <?php $total += ($producto->cantidad * $producto->precio); ?>
            @endforeach
            <tr>
                <td style="text-align:center; margin-right: 3%;border: 1px solid #000; font-weight:bold">Total
                </td>
                <td style="text-align:center; margin-right: 3%;border: 1px solid #000"></td>
                <td style="text-align:center; margin-right: 3%;border: 1px solid #000"></td>
                <td style="text-align:center; margin-right: 3%;border: 1px solid #000; font-weight:bold">
                    ${{number_format($total, 2)}}
                </td>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>