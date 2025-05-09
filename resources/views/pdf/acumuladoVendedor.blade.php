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
            table, th, td {
                border: 1px solid #333 !important;
                border-collapse: collapse;
            }
            th, td {
                padding: 8px;
                text-align: center;
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
        border: 1px solid #333;
    }
    .client-table tr {
        border: 1px solid #333;
    }
    .client-table td {
        width: 50%;
        vertical-align: top;
        padding: 4px;
        font-size: 14px;
        border: 1px solid #333;
    }
    .client-item {
        margin-bottom: 8px;
        page-break-inside: avoid;
    }
</style>
<style>
    .page-break {
        page-break-after: avoid;
    }
</style>
<div class="page-break"></div>
<div class="container">
<?php
$half = ceil(count($ventasXVend) / 2);

if ($half > 23) {
    $third = ceil(count($ventasXVend) / 3);
    $left = array_slice($ventasXVend, 0, $third);
    $middle = array_slice($ventasXVend, $third, $third);
    $right = array_slice($ventasXVend, $third * 2);
    $leftTotals = array_slice($ventasXVendTotales, 0, $third);
    $middleTotals = array_slice($ventasXVendTotales, $third, $third);
    $rightTotals = array_slice($ventasXVendTotales, $third * 2);
    $maxRows = max(count($left), count($middle), count($right));
} else {
    $left = array_slice($ventasXVend, 0, $half);
    $right = array_slice($ventasXVend, $half);
    $leftTotals = array_slice($ventasXVendTotales, 0, $half);
    $rightTotals = array_slice($ventasXVendTotales, $half);
    $maxRows = max(count($left), count($right));
}
?>

<table class="client-table">
    @for ($i = 0; $i < $maxRows; $i++)
        <tr>
            <td>
                @if(isset($left[$i]))
                    <strong>{{ $i + 1 }}. Cliente:</strong> {{ $left[$i] }}<br>
                    <strong>Total:</strong> ${{ $leftTotals[$i] }}
                @endif
            </td>
            @if ($half > 23)
                <td>
                    @if(isset($middle[$i]))
                        <strong>{{ $i + 1 + count($left) }}. Cliente:</strong> {{ $middle[$i] }}<br>
                        <strong>Total:</strong> ${{ $middleTotals[$i] }}
                    @endif
                </td>
            @endif
            <td>
                @if(isset($right[$i]))
                    <strong>{{ $i + 1 + ($half > 23 ? count($left) + count($middle) : count($left)) }}. Cliente:</strong> {{ $right[$i] }}<br>
                    <strong>Total:</strong> ${{ $rightTotals[$i] }}
                @endif
            </td>
        </tr>
    @endfor
</table>

    <h3>Total: ${{ $total }}</h3>
</div>

<style>
    .client-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 24px;
        text-align: left;
    }
    .client-table td, .client-table th {
        border: 1px solid #333;
        padding: 8px;
        vertical-align: top;
        font-size: 15px;
    }
</style>
