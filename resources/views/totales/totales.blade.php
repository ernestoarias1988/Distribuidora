@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")
<?php
$productosAcum = array();
$productosAcumCant = array();
$productosAcumPrecio = array();
$ventasXVend = array();
$ventasXVendTotales = array();
$total = 0;
for ($i = 0; $i < 1000; $i++) {
    $ventasXVendTotales[$i] = 0;
    $productosAcum[$i] = null;
    $productosAcumCant[$i] = null;
    $productosAcumPrecio[$i] = null;
    $productosAcumCodigo[$i] = null;
}
?>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <h2>Acumulado de {{$vendedor}}</h2>
        @include("notificacion")
        <a class="btn btn-primary" target="blank" style="margin-top:-0.5%" href="{{route("ventasVendedor.pdf", ["id"=>$vendedor])}}">
            <i class="fa fa-print"></i>&nbsp; Imprimir tickets por Vendedor
        </a>
        <a class="btn btn-warning" target="blank" style="margin-top:-0.5%" href="{{ route('ventas.index') }}">
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
                            if ($venta->entregado == 0) {
                                // echo "$venta->vendedor";
                                $ventasXVend[$j] = $venta->cliente->nombre;
                                foreach ($venta->productos as $producto) {
                                    for ($i = 0; $i < 1000; $i++) {
                                        if ($producto->descripcion == $productosAcum[$i]) {
                                            $productosAcumCant[$i] += $producto->cantidad;
                                            $i = 1000;
                                        } else {
                                            if ($productosAcum[$i] == null) {
                                                $productosAcumCant[$i] = $producto->cantidad;
                                                $productosAcum[$i] = $producto->descripcion;
                                                $productosAcumCodigo[$i] = $producto->codigo_barras;
                                                $productosAcumPrecio[$i] = $producto->precio;
                                                $i = 1000;
                                            }
                                        }
                                    }
                                    //echo "<tr><td>$producto->codigo_barras</td></tr>";
                                }
                                $j++;
                            }
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
                    for ($i = 0; $i < 1000; $i++) {
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

                    @foreach($ventasXVend as $cliente)
                    <h5><strong>Cliente:</strong> {{$cliente}} <strong>Total:</strong> ${{$ventasXVendTotales[$ventasInd]}} </h5>
                    <?php
                    $ventasInd++;
                    ?>
                    @endforeach
                    <h3>Total: ${{$total}}</h3>
        </div>
    </div>
    @endsection