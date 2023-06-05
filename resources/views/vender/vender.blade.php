@extends("maestra")
@section("titulo", "Realizar venta")
@section("contenido")


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="row">
    <div class="col-12">
        <h1>Nueva venta <i class="fa fa-cart-plus"></i></h1>
        @include("notificacion")
        <div class="col-12">
            <div class="col-12 col-md-6">
                <form action="{{route("guardarCliente")}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="id_cliente">Cliente</label>
                        <input type="text" autocomplete="off" required class="form-control" name="id_cliente" id="id_cliente" placeholder="Ingrese el Cliente antes de finalizar la venta" />
                        <div id="clientelist">
                        </div>
                    </div>
                    <button name="accioncliente" type="submit" class="btn btn-primary">Seleccionar Cliente
                    </button>
            </div>
        </div>
        </form>
        @if(session("cliente") !== null)
        <div class="col-12 col-md-6">
            <form action="{{route("editaCantidad")}}" method="post">
                @csrf
                <div class="form-group">
                    <label for="descripcion">Producto</label>
                    <input type="text" name="codigo" autocomplete="off" id="codigo" class="form-control" required autofocus name="codigo" placeholder="Ingrese el producto" />
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
            </form>
        </div>
    </div>


</div>
<h2><br>Cliente: {{$cliente->nombre}} </h2>
@if(session("productos") !== null)
<h2><br>Total:${{number_format($total, 2)}}</h2>
<form action="{{route("terminarOCancelarVenta")}}" method="post">
    @csrf

    <div>
        <div class="form-group">
            <button name="accion" value="terminar" type="submit" class="btn btn-success">Terminar
                venta
            </button>
            <button name="accion" value="cancelar" type="submit" class="btn btn-danger">Cancelar
                venta
            </button>
        </div>
</form>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código de barras</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Quitar</th>
            </tr>
        </thead>
        <tbody>
            @foreach(session("productos") as $producto)
            <tr>
                <td>{{$producto->codigo_barras}}</td>
                <td>{{$producto->descripcion}}</td>
                <td><?php $precioactual = 0; ?>
                    @if(session("cliente") !== null)
                    <?php
                    if ($cliente) {
                        switch ($cliente->lista) {
                            case "1":
                                echo "$producto->precio_venta1";
                                $precioactual = $producto->precio_venta1;
                                break;

                            case "2":
                                echo "$producto->precio_venta2";
                                $precioactual = $producto->precio_venta2;
                                break;

                            case "3":
                                echo "$producto->precio_venta3";
                                $precioactual = $producto->precio_venta3;
                                break;
                        }
                    } else {
                        echo "Seleccione Cliente";
                    }
                    ?>
                    @endif
                    @if(session("cliente") == null)
                    Seleccione Cliente
                    @endif
                </td>
                <td>{{$producto->cantidad}}</td>
                <td>
                    <?php
                    $total = $producto->cantidad * $precioactual;
                    echo "$" . $total . "";
                    ?>
                </td>
                <td>
                    <form action="{{route("quitarProductoDeVenta")}}" method="post">
                        @method("delete")
                        @csrf
                        <input type="hidden" name="indice" value="{{$loop->index}}">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@else
<h2>Aquí aparecerán los productos de la venta
    <br>
</h2>
@endif
</div>
</div>







<script>
    $('#codigo').ready(function() {

        $('#codigo').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetch')}}",
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
            $('#codigo').val($(this).text());
            $('#descripcionlist').fadeOut();
        });


    });



    $('#descripcionlist').on('click', function() {
        var query = $(document.getElementById("codigo")).val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "{{ route('autocomplete.fetchcantidad')}}",
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


<script>
    $('#id_cliente').ready(function() {

        $('#id_cliente').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token2 = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetchcliente') }}",
                    method: "POST",
                    data: {
                        query: query,
                        _token: _token2
                    },
                    success: function(data) {
                        $('#clientelist').fadeIn();
                        $('#clientelist').html(data);
                    }
                });
            }
        });

        $('#clientelist').on('click', 'li', function() {
            $('#id_cliente').val($(this).text());
            $('#clientelist').fadeOut();
        });

    });
</script>



@endsection