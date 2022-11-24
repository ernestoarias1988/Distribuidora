<?php

use App\Venta;
use Illuminate\Http\Request;

//["ventas" => Venta::all()];
//$localidad = $data['localidad'];
$ventas = $data['ventas'];
$localidad = $data['localidad'];
$total = 0;
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
            @foreach($ventas->sortBy('created_at') as $venta)
            @if(($venta->cliente->localidad==$localidad || $localidad==='Todas' || $localidad==null) && $venta->entregado != 1)
            <h4>Venta:#{{$venta->id}}</h4>
            Vendedor:{{$venta->vendedor}}<br>
            Cliente: {{$venta->cliente->nombre}}<br>
            Direccion: {{$venta->cliente->direccion}} - Localidad: {{$venta->cliente->localidad}}<br>
            <br>

            <table style="text-align: center; width:100%;">
                <thead>
                    <tr>
                        <th style="text-align:left">Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>SubTotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->productos as $producto)
                    <tr>
                        <td style="text-align:left">{{$producto->descripcion}} </td>
                        <td> {{$producto->cantidad}} U. </td>
                        <td> ${{number_format($producto->precio, 2)}}</td>
                        <td> ${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
                    </tr>
                    <?php $total += ($producto->cantidad * $producto->precio); ?>
                    @endforeach
                </tbody>
            </table>
            <h4 style="text-align:right; margin-right: 3%">Total: ${{number_format($total, 2)}}</h4>
            <?php $total = 0; ?>
            --------------------------------------------------------------------------------------------------<br>
            @endif
            @endforeach

</body>

</html>