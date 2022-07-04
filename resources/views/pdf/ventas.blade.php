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
            Venta:#{{$venta->id}}<br>
            Vendedor:{{$venta->vendedor}}<br>
            Cliente: {{$venta->cliente->nombre}}<br>
            Productos: <br>
            @foreach($venta->productos as $producto)
            {{$producto->descripcion}} | {{number_format($producto->cantidad, 0)}} unidades | ${{number_format($producto->cantidad * $producto->precio, 2)}}<br>
            <?php $total += ($producto->cantidad * $producto->precio); ?>
            @endforeach
            Total: ${{number_format($total, 2)}}
            <?php $total = 0; ?>
            <br>-----------------------------------------------------------------------------------------<br>
            @endif
            @endforeach

</body>

</html>