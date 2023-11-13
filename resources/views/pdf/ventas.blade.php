<?php

use App\Venta;
use Illuminate\Http\Request;

$ventas = $data['ventas'];
$localidad = $data['localidad'];
$total = 0;
$duplicados = [1,2];

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
<style type="text/css">
    table { page-break-inside:auto }
    div   { page-break-inside:avoid; } /* This is the key */
    thead { display:table-header-group }
    tfoot { display:table-footer-group } 
</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 ">
                <h1></h1>
            </div>
            @php $check=0 @endphp
            @foreach($ventas->sortBy('created_at') as $venta)
            @if(($venta->cliente->localidad==$localidad || $localidad==='Todas' || $localidad==null) && $venta->entregado != 1)
            <table style="text-align: center; width:50%;  font-size:85%; margin: 0px">
            <tbody >
<tr>
    @foreach($duplicados as $duplicadoo)
    <td style = "margin: 0px; margin-left:1px;  ">

            <h3 style="text-align: center; margin:0px">Distribuidora Dany</h3>

            <table style="text-align: center; width:100%; border-collapse: collapse; font-size:85%;">

                <thead>
                <tr>
                <th style="text-align: left; border: 1px solid #000;border-right: 1px solid #fff; font-weight:10">Cliente: {{$venta->cliente->nombre}}</th>
                <th style="border: 1px solid #000; border-right: 1px solid #000;"></th>
                <th style="border: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #fff;"></th>
                <th style="border: 1px solid #000; text-align: rigth;"> Fecha: <?php echo date("d/m/Y"); ?>
                </th>
            </tr>
            <tr>
                <th style="text-align: left; font-weight:10"><strong>Presupuesto</strong><br>{{$venta->cliente->direccion}}<br>Localidad: {{$venta->cliente->localidad}} <br>Vendedor: {{$venta->vendedor}}</th>
                <th></th>
                <th>
                        </th>
                        <th> <img style="width:80px;" src="{{url("/img/logo.png")}}">

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
                <?php
                $total=0;
                $productCount = 0;
                ?>
                    @foreach($venta->productos as $producto)
                    <tr>
                        <td style="border: 1px solid #000"> {{$producto->cantidad}} U. </td>
                        <td style="text-align:left; border: 1px solid #000">{{$producto->descripcion}} </td>
                        <td style=" border: 1px solid #000"> ${{number_format($producto->precio, 2)}}</td>
                        <td style="border: 1px solid #000"> ${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
                    </tr>
                    <?php 
                    $total += ($producto->cantidad * $producto->precio);
                    $productCount++; 
                    ?>
                    @if($productCount > 10 )
                    @php echo '<div style="    page-break-after: always;"></div>'; $productCount = 0; @endphp
                    @endif
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

            --------------------------------------------------------------------------------------------------
            @php $check++ @endphp
            <!-- Salto de pagina cada 2 ventas -->
            @if( $check % 2 == 0 )
            @php echo '<div style="page-break-after: always;"></div>';  @endphp
            @endif
            </td>             
            @if( $check % 2 != 0 )
            <td style="color: white">-----</td>
            @endif

        @endforeach
        
</tr>
</tbody>
</table>     
      
 @endif

@endforeach

</body>

</html>