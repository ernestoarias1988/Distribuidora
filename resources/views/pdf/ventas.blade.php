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
            @php $check=0 @endphp
            @foreach($ventas->sortBy('created_at') as $venta)
            @if(($venta->cliente->localidad==$localidad || $localidad==='Todas' || $localidad==null) && $venta->entregado != 1)
            <h5 style="margin-bottom:0% ; margin-top:1%">Venta: #{{$venta->id}}</h5>
            <u>Vendedor:</u> {{$venta->vendedor}}<br>
            <u>Cliente:</u> {{$venta->cliente->nombre}} - <u>Direccion:</u> {{$venta->cliente->direccion}} - <u>Localidad:</u> {{$venta->cliente->localidad}}<br>
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
            @php $check++ @endphp
            <!-- Salto de pagina cada 2 ventas -->
            @if( $check % 2 == 0 )
            @php echo '<div style="page-break-after: always;"></div>'; @endphp
            @endif
            @endif
            @endforeach

</body>

</html>