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
$productosAcum = array();
$productosAcumCant = array();
$productosAcumPrecio = array();
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
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>Precio X Cantidad</th>
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
                                <td>$productosAcumCantNEW[$i]</td> 
                                <td>$productosAcumNEW[$i]</td>
                                <td>$$productosAcumPrecioNEW[$i]</td>
                                <td>$$precio</td> 
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
                                $indexVend = array_search($cliente, $ventasXVend);
                                $ventasXVendTotales[$indexVend] += $venta->total;
                            }
                        }
                    }
                    $ventasInd = 0;
                    ?>
                    @endforeach
<?php
$i= 1;
?>
 
        </div>
        
    </div>