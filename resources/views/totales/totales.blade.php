@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")
<style>
    .sin-stock-row {
        background-color: #f8d7da;
    }
    .sin-stock-badge {
        display: inline-block;
        margin-left: 0.4rem;
        padding: 0.15rem 0.45rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #721c24;
        background-color: #f5c6cb;
        border: 1px solid #f1b0b7;
    }
</style>
<?php
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
        @include("notificacion")
        <a class="btn btn-primary" target="blank" style="margin-top:-0.5%" href="{{route("ventasVendedor.pdf", ["id"=>$vendedor,"localidad"=>$localidad])}}">
            <i class="fa fa-print"></i>&nbsp; Imprimir tickets por Vendedor
        </a>
        <a class="btn btn-primary" target="blank" style="margin-top:-0.5%" href="{{route("acumuladoVendedor.pdf", ["id"=>$vendedor,"localidad"=>$localidad])}}">
            <i class="fa fa-print"></i>&nbsp; Imprimir Totales del Vendedor
        </a>
        <a class="btn btn-warning" style="margin-top:-0.5%" href="{{ route('ventas.index') }}">
            <i class="fa fa-print"></i>&nbsp; Volver a ventas
        </a>

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
                            $stockActual = $stockPorCodigo[$productosAcumCodigoNEW[$i]] ?? null;
                            $sinStock = $stockActual !== null && $stockActual < 0;
                            $rowClass = $sinStock ? ' class="sin-stock-row"' : '';
                            $descripcion = $productosAcumNEW[$i];
                            if ($sinStock) {
                                $descripcion .= ' <span class="sin-stock-badge">SIN STOCK: ' . $stockActual . '</span>';
                            }
                            echo "<tr$rowClass>
                                <td>$productosAcumCantNEW[$i]</td> 
                                <td>$descripcion</td>
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
                    @foreach($ventasXVend as $cliente)
                    <h5><strong>{{$i}}. Cliente:</strong> {{$cliente}} <strong>Total:</strong> ${{$ventasXVendTotales[$ventasInd]}} </h5>
                    <?php
                    $ventasInd++;
                    $i++;
                    ?>
                    @endforeach
                    <h3>Total: ${{$total}}</h3>
        </div>
    </div>
    @endsection