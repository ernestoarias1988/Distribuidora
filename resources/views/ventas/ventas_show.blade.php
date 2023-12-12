@extends("maestra")
@section("titulo", "Detalle de venta ")
@section("contenido")


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


<div class="row">
    <div class="col-12">
        <h1>Detalle de venta #{{$venta->id}}</h1>
        <h2>Cliente: <small>{{$venta->cliente->nombre}}</small></h2>
        <h3>Direccion: <small>{{$venta->cliente->direccion}} - {{$venta->cliente->localidad}}</small></h3>
        <h3>Vendedor: <small>{{$venta->vendedor}}</small></h3>
        @include("notificacion")
        <a class="btn btn-info" href="{{route("ventas.index")}}">
            <i class="fa fa-arrow-left"></i>&nbsp;Volver
        </a>
        <a class="btn btn-success" style="margin:5px ;" target="blank" href="{{route("users.pdf", ["id"=>$venta->id])}}">
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
                            <input type="number" step="0.1" $ required value="{{number_format($producto->cantidad, 2)}}" required class="form-control" name="cantidad" id="cantidad" placeholder=""></p>
                        </form>
                    </td>
                    <td>${{number_format($producto->cantidad * $producto->precio, 2)}}
                    </td>
                    <td>
                        @method("post")
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="message($producto);">
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>
                </tr>
                @endforeach
    </div>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Total</strong></td>
                    <td>${{number_format($total, 2)}}</td>
                </tr>
            </tfoot>
        </table>
        <div class="col-12 col-md-6">
            <form action="{{route("editaVenta")}}" method="post">
                @csrf
                <div class="form-group">
                 <label for="descripcion">Agregar nuevo producto</label>
                    <input type="text" name="codigo2" autocomplete="off" id="codigo2" class="form-control" required autofocus name="codigo2" placeholder="Ingrese el producto" />
                    <div id="descripcionlist">
                    </div>
                </div>
                <div <p id="existencia">
                    </p>
                </div>
                {{ csrf_field() }}
                @csrf
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" step="0.1" min="0" name="cantidad" autocomplete="off" id="cantidad" class="form-control" required autofocus name="cantidad" placeholder="Cantidad" />
                </div>
                {{ csrf_field() }}
                <button type="submit" class="btn btn-primary">Agregar Producto &nbsp;
                    <i class="fa fa-plus-square fa-2x" aria-hidden="true"></i> </button>
                <input type="text" readonly name="idventa" id="idventa"  style = "width:10%; border-color:white; color:white;     border-width:0px " required  name="idventa" value= "{{$venta->id}}"  />
                <input type="text" readonly name="lista" id="lista" required style = "width:10%; border-color:white; color:white; border-width:0px" name="lista" value= "{{$venta->cliente->lista}}"  />
                   
            </form>
        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>

    
    function message($producto) {
       // $producto->$cantidad = 0;
        alert("Datos: "+ "s");

    }

    $('#codigo2').ready(function() {

        $('#codigo2').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetchVentas')}}",
                    method: "POST",
                    data: {
                        query: query,
                        _token: _token
                    },
                    success: function(data) {
                        $('#descripcionlist').fadeIn();
                        $('#descripcionlist').html(data);
                    }
                });
            }
        });

        $('#descripcionlist').on('click', 'li', function() {
            $('#codigo2').val($(this).text());
            $('#descripcionlist').fadeOut();
        });


    });


    </script>

    <script>


    $('#descripcionlist').on('click', function() {
        var query = $(document.getElementById("codigo2")).val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('autocomplete.fetchcantidadVentas')}}",
            method: "POST",
            data: {
                query: query,
                _token: _token
            },
            success: function(data) {
                document.getElementById("existencia").textContent = data;
            }
        });
    });
</script>

