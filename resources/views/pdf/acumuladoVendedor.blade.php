<?php

use App\Venta;
use Illuminate\Http\Request;

$ventas = $data['ventas'];
$vendedor = $data['vendedor'];
$localidad = $data['localidad'];
$total = 0;
$totalDeVenta = 0;
$duplicados = [1,2];
$productosAcum = array();
$productosAcumCant = array();
$productosAcumPrecio = array();
$productosAcumCodigo = array();
$ventasXVend = array();
$ventasXVendTotales = array();
$total = 0;
for ($i = 0; $i < 10000; $i++) {
    $ventasXVendTotales[$i] = 0;
    $productosAcum[$i] = null;
    $productosAcumCant[$i] = null;
    $productosAcumPrecio[$i] = null;
    $productosAcumCodigo[$i] = null;
}
?>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <h2>Acumulado de {{$vendedor}} de {{$localidad}}<?php $fecha = date("d-m-Y");
        echo " del $fecha";
        ?>
        </h2>
        <div class="table-responsive">
            <style>
            @page {
                size: A4 landscape;
            }
            table {
                font-size: 14px;
                width: 100%; /* Make the table use all the width of the page */
            }
            </style>
            <table class="table table-bordered">
            <thead>
                <tr>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 50%;">Descripción</th>
                <th style="width: 20%;">Precio Unitario</th>
                <th style="width: 20%;">Precio X Cantidad</th>
                </tr>
            </thead>
            <tbody>
                    <?php
                    $j = 0;
                    foreach ($ventas as $venta) {
                        if ($venta->vendedor == $vendedor) {
                            if($venta->cliente->localidad == $localidad || $localidad == "Todas"){
                              if ($venta->entregado == 0) {
                                $ventasXVend[$j] = $venta->cliente->nombre;
                                foreach ($venta->productos as $producto) {
                                    for ($i = 0; $i < 10000; $i++) {
                                        if ($producto->codigo_barras == $productosAcumCodigo[$i]) {
                                            $productosAcumCant[$i] += $producto->cantidad;
                                            $i = 10000;
                                        } else {
                                            if ($productosAcum[$i] == null) {
                                                $productosAcumCant[$i] = $producto->cantidad;
                                                $productosAcum[$i] = $producto->descripcion;
                                                $productosAcumCodigo[$i] = $producto->codigo_barras;
                                                $productosAcumPrecio[$i] = $producto->precio;
                                                $i = 10000;
                                            }
                                        }
                                    }
                                    //echo "<tr><td>$producto->codigo_barras</td></tr>";
                                }
                                $j++;
                            }}
                        }
                    }
                    $ventasXVend = array_unique($ventasXVend);
                    $ventasXVend = array_values($ventasXVend);
                    ?>
                    <?php

                    $productosAcumCodigoNEW = array_values(array_filter($productosAcumCodigo));
                    sort($productosAcumCodigoNEW, SORT_NUMERIC);
                    for ($i = 0; $i < sizeof($productosAcumCodigoNEW); $i++) {
                        if ($productosAcumCodigoNEW[$i] != null) {
                            $index = array_search($productosAcumCodigoNEW[$i], $productosAcumCodigo);
                            $productosAcumNEW[$i] = $productosAcum[$index];
                            $productosAcumPrecioNEW[$i] = $productosAcumPrecio[$index];
                            $productosAcumCantNEW[$i] = $productosAcumCant[$index];
                        }
                    }
                    for ($i = 0; $i < 10000; $i++) {
                        if ($productosAcum[$i] != null && $productosAcumCodigo[$i] != null) {
                            $precio = $productosAcumPrecioNEW[$i] * $productosAcumCantNEW[$i];
                            echo "<tr>
                                <td style='width: 10%; text-align: center;'>$productosAcumCantNEW[$i]</td> 
                                <td style='width: 50%; text-align: center;'>$productosAcumNEW[$i]</td>
                                <td style='width: 20%; text-align: center;'>$$productosAcumPrecioNEW[$i]</td>
                                <td style='width: 20%; text-align: center;'>$$precio</td> 
                                </tr>";
                            $total += $precio;
                        }
                    }
                    ?>

                    @foreach($ventasXVend as $cliente)
                    <?php
                    foreach ($ventas as $venta) {
                        if ($venta->vendedor == $vendedor && $venta->entregado == 0) {
                            if ($venta->cliente->nombre == $cliente) {
                                $totalDeVenta = 0;
                                foreach ($venta->productos as $producto) {
                                    $precio = $producto->precio * $producto->cantidad;
                                    $totalDeVenta += $precio;
                                }
                                $indexVend = array_search($cliente, $ventasXVend);
                                $ventasXVendTotales[$indexVend] += $totalDeVenta;
                            }
                        }
                    }
                    ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



<style>
    .container {
        width: 100%;
    }
    .client-table {
        width: 100%;
        border-collapse: collapse;
    }
    .client-table td {
        width: 50%; /* Each column takes 50% */
        vertical-align: top;
        padding: 4px;
        font-size: 14px;

    }
    .client-item {
        margin-bottom: 8px;
        page-break-inside: avoid;
    }
</style>
<style>
    .page-break {
        page-break-after: always;
    }
</style>
<div class="page-break"></div>
<div class="container">
    <?php
    $half = ceil(count($ventasXVend) / 2);
    ?>

    <table class="client-table">
        <tr>

            <td>
                @for ($i = 0; $i < $half; $i++)
                    <p class="client-item"><strong>{{ $i + 1 }}. Cliente:</strong> {{ $ventasXVend[$i] }}
                        <strong>Total:</strong> ${{ $ventasXVendTotales[$i] }}
                    </p>
                    
                @endfor
            </td>
            <td>
                @for ($i = $half; $i < count($ventasXVend); $i++)
                    <p class="client-item"><strong>{{ $i + 1 }}. Cliente:</strong> {{ $ventasXVend[$i] }}
                        <strong>Total:</strong> ${{ $ventasXVendTotales[$i] }}
                    </p>
                @endfor
            </td>
        </tr>
    </table>

    <h3>Total: ${{ $total }}</h3>
</div>