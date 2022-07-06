@extends("maestra")
@section("titulo", "Detalle de venta ")
@section("contenido")
<div class="row">
    <div class="col-12">
        <h1>Detalle de venta #{{$venta->id}}</h1>
        <h1>Cliente: <small>{{$venta->cliente->nombre}}</small></h1>
        <h1>Direccion: <small>{{$venta->cliente->direccion}}</small></h1>
        <h1>Localidad: <small>{{$venta->cliente->localidad}}</small></h1>
        <h1>Vendedor: <small>{{$venta->vendedor}}</small></h1>
        @include("notificacion")
        <a class="btn btn-info" href="{{route("ventas.index")}}">
            <i class="fa fa-arrow-left"></i>&nbsp;Volver
        </a>
        <a class="btn btn-success" target="blank" href="{{route("users.pdf", ["id"=>$venta->id])}}">
            <!--, ["id" => $venta->id]) -->
            <i class="fa fa-print"></i>&nbsp;PDF
        </a>
        <form action="{{route("ventas.destroy", [$venta])}}" method="post">
            @method("delete")
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fa fa-trash"></i>
                Eliminar Pedido
            </button>
        </form>
        <h2>Productos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Código de barras</th>
                    <th>Precio</th>
                    <th style="width: 10%;">Cantidad</th>
                    <th>Subtotal</th>
                    <th>Eliminar Producto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->productos as $producto)
                <tr>
                    <td>{{$producto->descripcion}}</td>
                    <td>{{$producto->codigo_barras}}</td>
                    <td>${{number_format($producto->precio, 2)}}</td>
                    <td>
                        <form action="{{route('cargaCantidadShow', ["id"=>$venta->id,"descripcion"=>$producto->descripcion])}}" method="post">
                            {{ csrf_field() }}
                            @csrf
                            <input type="number" step="1" $ required value="{{number_format($producto->cantidad, 0)}}" required class="form-control" name="cantidad" id="cantidad" placeholder=""></p>
                        </form>
                    </td>
                    <td>${{number_format($producto->cantidad * $producto->precio, 2)}}
                    </td>
                    <td>
                        @method("post")
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Total</strong></td>
                    <td>${{number_format($total, 2)}}</td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
@endsection