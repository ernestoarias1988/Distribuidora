@extends("maestra")
@section("titulo", "Acumulados")
@section("contenido")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="row" style="margin-left:1%;">
    <div class="col-12">
        <h1>Acumulados <i class="fa fa-list"></i></h1>
        <a class="btn btn-warning" target="blank" style="margin-top:-0.5%" href="{{route('ventas.index') }}">
            <i class="fa fa-print"></i>&nbsp; Volver a ventas
        </a>
    </div>
</div>
<div style="text-align:left; margin-left:2%; font-size:large">
    <?php
    foreach ($ventas as $venta) {
        foreach ($vendedores as $vendedor) {
            if (($venta->vendedor == $vendedor->email || $venta->vendedor == $vendedor->email) && $venta->entregado == 0) {
                $vendedor->total += $venta->total;
                //echo "{$vendedor->total}";
            }
        }
    }
    $i=1;
    foreach ($vendedores as $vendedor) {
        if ($vendedor->total > 0) {
            echo "$i. ";
            $i++;
            echo "{$vendedor->name}: $";
            echo "{$vendedor->total}<br>";
        }
    }
    ?>


</div>