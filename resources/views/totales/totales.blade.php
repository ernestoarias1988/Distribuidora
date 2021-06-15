@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")
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
                        echo"$producto->descripcion x $producto->cantidad <br>";
                    }
                }
            }
            ?>            
            @endforeach
            </h2>
        </div>
    </div>
@endsection