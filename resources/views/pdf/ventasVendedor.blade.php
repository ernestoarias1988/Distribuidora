<?php

use App\Venta;
use Illuminate\Http\Request;

//["ventas" => Venta::all()];
//$localidad = $data['localidad'];
$ventas = $data['ventas'];
$vendedor = $data['vendedor'];
$localidad = $data['localidad'];
$total = 0;
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
                @php $check=0 @endphp
                @foreach($ventas->sortBy('created_at') as $venta)
                    @if($venta->cliente && $venta->productos && $venta->productos->count() > 0)
                        <table style="text-align: center; width:50%; border-collapse: collapse; font-size:80%; margin: 5px">
                            <tbody>
                                <tr>
                                    @foreach($duplicados as $duplicadoo)
                                        <td style="margin-left:5px">
                                            <h3 style="text-align: center; margin:2px">Distribuidora Dany</h3>
                                            @if($venta->productos->count() > 25)
                                                <table style="text-align: center; width:100%; border-collapse: collapse; font-size:70%;">
                                            @endif
                                            @if($venta->productos->count() <= 25)
                                                <table style="text-align: center; width:100%; border-collapse: collapse; font-size:80%;">
                                            @endif
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: left; border: 1px solid #000;border-right: 1px solid #fff; font-weight:10">Cliente: {{$venta->cliente->nombre}}</th>
                                                        <th style="border: 1px solid #000; border-right: 1px solid #000;"></th>
                                                        <th style="border: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #fff;"></th>
                                                        <th style="border: 1px solid #000; text-align: rigth;"> Fecha: <?php echo date("d/m/Y"); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th style="text-align: left; font-weight:10"><strong>Presupuesto</strong><br>{{$venta->cliente->direccion}}<br>Localidad: {{$venta->cliente->localidad}} <br>Vendedor: {{$venta->vendedor}}</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th> <img style="width:80px; " src="{{url("/img/logo.png")}}"></th>
                                                    </tr>
                                                    <tr><th style="text-align: left; font-weight:10"></th></tr>
                                                    <tr><th style="text-align: left; font-weight:10"></th></tr>
                                                    <tr style="border: 1px solid #000; text-align: left;  font-weight:10">
                                                        <th style="border: 1px solid #000; width: 3%;">Cantidad</th>
                                                        <th style="text-align:left; border: 1px solid #000; width: 60%;">Descripción</th>
                                                        <th style="border: 1px solid #000; width: 22%;">Precio unitario</th>
                                                        <th style="border: 1px solid #000; width: 15%;">SubTotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="border: 1px solid #000; ">
                                                    <?php $total=0; ?>
                                                    @foreach($venta->productos as $producto)
                                                        <tr>
                                                            <td style="border: 1px solid #000; width: 3%;"> {{$producto->cantidad}} U. </td>
                                                            <td style="text-align:left; border: 1px solid #000; width: 60%;">{{$producto->descripcion}} </td>
                                                            <td style="border: 1px solid #000; width: 22%;"> ${{number_format($producto->precio, 2)}}</td>
                                                            <td style="border: 1px solid #000; width: 15%;"> ${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
                                                        </tr>
                                                        <?php $total += ($producto->cantidad * $producto->precio); ?>
                                                    @endforeach
                                                    <tr>
                                                        <td style="text-align:center; width: 3%; margin-right: 3%;border: 1px solid #000; font-weight:bold">Total</td>
                                                        <td style="text-align:center; width: 60%; margin-right: 3%;border: 1px solid #000"></td>
                                                        <td style="text-align:center; width: 22%; margin-right: 3%;border: 1px solid #000"></td>
                                                        <td style="text-align:center; width: 15%; margin-right: 3%;border: 1px solid #000; font-weight:bold">${{number_format($total, 2)}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            --------------------------------------------------------------------------------------------------
                                            @php $check++ @endphp
                                            <!-- Salto de pagina cada 2 ventas -->
                                            @if( $check % 2 == 0 )
                                                @php echo '<div style="page-break-after: always;"></div>'; @endphp
                                            @endif
                                        </td>
                                        @if( $check % 2 != 0 )
                                            <td style="color: white">------------------</td>
                                        @endif
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>