@extends("maestra")
@section("titulo", "Realizar venta")
@section("contenido")

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .venta-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .venta-header h1 {
        margin: 0;
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
    }
    .venta-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        padding: 18px 12px; /* smaller padding */
        margin-bottom: 24px; /* less margin */
    }
    .venta-form label {
        font-weight: 600;
        color: #34495e;
        font-size: 0.95rem;
    }
    .venta-form input[type="text"],
    .venta-form input[type="number"] {
        border-radius: 6px;
        border: 1px solid #ced4da;
        font-size: 0.95rem;
        padding: 4px 8px;
        height: 32px;
    }
    .venta-form .btn {
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 6px 0;
    }
    .venta-table th, .venta-table td {
        vertical-align: middle !important;
        text-align: center;
    }
    .venta-table th {
        background: #f8f9fa;
        font-weight: 700;
        color: #2c3e50;
    }
    .venta-table td {
        background: #fff;
    }
    .venta-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #27ae60;
        margin-top: 16px;
    }
    .venta-actions .btn {
        min-width: 140px;
        margin-right: 12px;
    }
    .venta-actions .btn:last-child {
        margin-right: 0;
    }
    .venta-empty {
        color: #888;
        font-size: 1.2rem;
        margin-top: 32px;
        text-align: center;
    }
    @media (max-width: 768px) {
        .venta-card { padding: 10px 4px; }
        .venta-header h1 { font-size: 1.5rem; }
    }
</style>

<div class="container-fluid">
    <div class="venta-header">
        <i class="fa fa-cart-plus fa-2x text-primary"></i>
        <h1>Nueva venta</h1>
    </div>
    @include("notificacion")
    <div class="row">
        <div class="col-md-6">
            <div class="venta-card">
                <form action="{{route('guardarCliente')}}" method="post" class="venta-form">
                    @csrf
                    <div class="form-group">
                        <label for="id_cliente">Cliente</label>
                        <input type="text" autocomplete="off" required class="form-control" name="id_cliente" id="id_cliente" placeholder="Ingrese el Cliente antes de finalizar la venta" />
                        <div id="clientelist"></div>
                    </div>
                    <button name="accioncliente" type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-user-check"></i> Seleccionar Cliente
                    </button>
                </form>
            </div>
        </div>
        @if(session("cliente") !== null)
        <div class="col-md-6">
            <div class="venta-card">
                <form action="{{route('editaCantidad')}}" method="post" class="venta-form">
                    @csrf
                    <div class="form-group">
                        <label for="codigo">Producto</label>
                        <input type="text" name="codigo" autocomplete="off" id="codigo" class="form-control" required autofocus placeholder="Ingrese el producto" />
                        <div id="descripcionlist"></div>
                    </div>
                    <div><p id="existencia"></p></div>
                    <div><p id="precio_venta1"></p></div>
                    <div class="form-group">
                        <input type="number" step="0.1" min="0" name="cantidad" autocomplete="off" id="cantidad" class="form-control" required placeholder="Cantidad" />
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa fa-plus-square"></i> Agregar Producto
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    @if(session("cliente") !== null)
        <div class="venta-card mt-4">
            <h4>Cliente: <span class="text-info">{{$cliente->nombre}}</span></h4>
            @if(session("productos") !== null)
                <div class="venta-total">Total: ${{number_format($total, 2)}}</div>
                <form action="{{route('terminarOCancelarVenta')}}" method="post">
                    @csrf
                    <div class="venta-actions my-3">
                        <button name="accion" value="terminar" type="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> Terminar venta
                        </button>
                        <button name="accion" value="cancelar" type="submit" class="btn btn-danger">
                            <i class="fa fa-times"></i> Cancelar venta
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table venta-table table-bordered">
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
                                <td>
                                    <?php $precioactual = 0; ?>
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
                                    <form action="{{route('quitarProductoDeVenta')}}" method="post">
                                        @method("delete")
                                        @csrf
                                        <input type="hidden" name="indice" value="{{$loop->index}}">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="venta-empty">Aquí aparecerán los productos de la venta</div>
            @endif
        </div>
    @else
        <div class="venta-empty">Aquí aparecerán los productos de la venta</div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(function() {
        // Autocomplete for cliente
        $('#id_cliente').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetchcliente') }}",
                    method: "POST",
                    data: { query: query, _token: _token },
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

        // Autocomplete for producto
        $('#codigo').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetch')}}",
                    method: "POST",
                    data: { query: query, _token: _token },
                    success: function(data) {
                        $('#descripcionlist').fadeIn();
                        $('#descripcionlist').html(data);
                    }
                });
        $('#descripcionlist').on('click', 'li', function() {
            $('#codigo').val($(this).text());
            $('#descripcionlist').fadeOut();
        });

        $('#descripcionlist').on('click', function() {
            var query = $(document.getElementById("codigo")).val();
            var _token = $('input[name="_token"]').val();
            var client = "{{ session('cliente') ? session('cliente')->id : '' }}";
            console.log("Cliente", client);
            // Fetch precio_venta1
            $.ajax({
                url: "{{ route('autocomplete.fetchprecio') }}",
                method: "POST",
                data: { query: query, _token: _token, client: client },
                success: function(data) {
                    $('#precio_venta1').text('Precio: $' + data);
                }
            });

            $.ajax({
                url: "{{ route('autocomplete.fetchcantidad')}}",
                method: "POST",
                data: { query: query, _token: _token },
                success: function(data) {
                    document.getElementById("existencia").textContent = data;
                }
            });
        });
            }
        });
    });


</script>
@endsection