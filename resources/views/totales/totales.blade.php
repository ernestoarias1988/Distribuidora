@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")
<?php
$productosAcum = array();
$productosAcumCant = array();
$productosAcumPrecio = array();
$total = 0;
for ($i = 0; $i < 1000; $i++) {
    $productosAcum[$i] = null;
    $productosAcumCant[$i] = null;
    $productosAcumPrecio[$i] = null;
}


?>
<div class="row">
    <div class="col-6">
        <h2>Acumulado de {{$vendedor}}</h2>

        @include("notificacion")
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
                    @foreach($ventas as $venta)
                    <?php
                    if ($venta->vendedor == $vendedor) {
                        if ($venta->entregado == 0) {
                            foreach ($venta->productos as $producto) {
                                for ($i = 0; $i < 1000; $i++) {
                                    //  echo"ANTES DEL IF  $producto->descripcion == $productosAcum[$i] <br>";
                                    if ($producto->descripcion == $productosAcum[$i]) {
                                        $productosAcumCant[$i] += $producto->cantidad;
                                        // $productosAcumPrecio[$i] += $producto->precio;
                                        //   echo"IF $productosAcum[$i] x $productosAcumCant[$i]<br>";
                                        $i = 1000;
                                    } else {
                                        if ($productosAcum[$i] == null) {
                                            $productosAcumCant[$i] = $producto->cantidad;
                                            $productosAcum[$i] = $producto->descripcion;
                                            $productosAcumPrecio[$i] = $producto->precio;
                                            //    echo"ELSE $productosAcum[$i] x $productosAcumCant[$i] <br>";
                                            $i = 1000;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    @endforeach
                    <?php
                    for ($i = 0; $i < 1000; $i++) {
                        if ($productosAcum[$i] != null) {
                            $precio = $productosAcumPrecio[$i] * $productosAcumCant[$i];
                            echo "<tr>
                                <td>$productosAcumCant[$i]</td> 
                                <td>$productosAcum[$i]</td>
                                <td>$$productosAcumPrecio[$i]</td>
                                <td>$$precio</td> 
                                </tr>";

                            $total += $productosAcumPrecio[$i] * $productosAcumCant[$i];
                        }
                    }
                    ?>
                    <h3>Total: ${{$total}}</h3>
        </div>


    </div>
    @endsection