@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")
<?php
$productosAcum = array ();
$productosAcumCant = array ();
for($i=0; $i<100; $i++)
{
    $productosAcum[$i] = null;
    $productosAcumCant[$i] = null;
}

?>
    <div class="row">
        <div class="col-12">
            <h1>Acumulado de {{$vendedor}}</h1>
            @include("notificacion")
            <h2>
            @foreach($ventas as $venta)
            <?php
            if($venta->vendedor==$vendedor)
            {
                if($venta->entregado==0)
                {
                    foreach($venta->productos as $producto){
                        for($i=0; $i<100; $i++)
                        {
                          //  echo"ANTES DEL IF  $producto->descripcion == $productosAcum[$i] <br>";
                            if($producto->descripcion == $productosAcum[$i])
                            {
                                $productosAcumCant[$i] += $producto->cantidad;
                             //   echo"IF $productosAcum[$i] x $productosAcumCant[$i]<br>";
                                $i = 100;
                            }else{
                                if($productosAcum[$i] == null)
                                {                                  
                                    $productosAcumCant[$i] = $producto->cantidad;
                                    $productosAcum[$i] = $producto->descripcion;
                                //    echo"ELSE $productosAcum[$i] x $productosAcumCant[$i] <br>";
                                    $i = 100;
                                }
                            }
                        }
                    }                    
                }
            }
            ?>            
            @endforeach
            <?php
            for($i=0; $i<100; $i++)
            {
                if($productosAcum[$i]!= null)
                {
                    echo"$productosAcum[$i] x $productosAcumCant[$i] <br>"; 
                }
            }                               
            ?>
            </h2>
        </div>
    </div>
@endsection