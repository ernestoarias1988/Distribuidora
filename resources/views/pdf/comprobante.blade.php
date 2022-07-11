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
        <!-- <div class="row">
            <div class="col-xs-10 ">
                <h1>Distribuidora</h1>
            </div>
            <div class="col-xs-2">
                <img style="max-width:10%;width:auto;height:auto;" class="img img-responsive" src="{{url("/img/vender.png")}}">
            </div>
        </div>
        <hr>   -->
        <div class="row">
            <div class="col-xs-10">
                <h1 class="h6">Comprobante de Pedido</h1>
            </div>
            <div class="col-xs-2 text-center">
                <strong>Fecha:</strong>

                <?php echo $fecha ?>
                <br>
                <strong>Pedido Nro:</strong>

                <?php echo $numero ?>
            </div>
        </div>
        <hr>
        <div class="row text-center" style="margin-bottom: 2rem;">
            <div class="col-xs-6">
                <h2 class="h2">Cliente</h2>
                <strong><?php echo $cliente ?></strong>
                <h3 class="h3">Direccion: <?php echo $direccion ?></h3>

                <div class="col-xs-6">
                    <h2 class="h2">Vendedor: <strong><?php echo $vendedor ?></strong></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table style="text-align: center;" width="100%" class="table table-condensed table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal = 0;
                            $request = $data['Request'];
                            $venta = Venta::findOrFail($request->get("id"));

                            foreach ($venta->productos as $producto) {
                                $totalProducto = $producto->cantidad * $producto->precio;
                                $subtotal += $totalProducto;
                            ?>
                                <tr>
                                    <td><?php echo $producto->descripcion ?></td>
                                    <td><?php echo number_format($producto->cantidad, 2) ?></td>
                                    <td>$<?php echo number_format($producto->precio, 2) ?></td>
                                    <td>$<?php echo number_format($totalProducto, 2) ?></td>
                                </tr>
                            <?php }
                            $subtotalConDescuento = $subtotal - $descuento;
                            //$impuestos = $subtotalConDescuento * ($porcentajeImpuestos / 100);
                            $total = $subtotalConDescuento /*+ $impuestos*/;
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right"><strong>Total</strong></td>
                                <td></td>
                                <td></td>
                                <td><strong>$<?php echo number_format($subtotal, 2) ?></strong></td>
                            </tr>
                            <!--<tr>
                    <td colspan="3" class="text-right">Descuento</td>
                    <td>$<?php echo number_format($descuento, 2) ?></td>
                </tr> 
                <tr>
                    <td colspan="3" class="text-right">Total con descuento</td>
                    <td>$<?php echo number_format($subtotalConDescuento, 2) ?></td>
                </tr>-->
                            <tr>
                                <!--<td colspan="3" class="text-right">Impuestos</td>-->
                                <!--<td>$<//?php echo number_format($impuestos, 2) ?></td>-->
                            </tr>

                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <p class="h5"><?php echo $mensajePie ?></p>
                </div>
            </div>
        </div>
</body>

</html>