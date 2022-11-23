@extends("maestra")
@section("titulo", "Ventas")
@section("contenido")
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="row">
    <div class="col-12">
        <h1>Ventas <i class="fa fa-list"></i></h1>
        @include("notificacion")
        <a class="btn btn-primary" target="blank" style="margin-top:-0.5%" href="{{route("ventas.pdf", ["id"=>$localidad])}}">
            <!--, ["id" => $venta->id]) -->
            <i class="fa fa-print"></i>&nbsp; Imprimir tickets por Localidad
        </a>
        <button style="text-align:center" class="btn btn-success mb-2" onClick="window.location.href='https://localhost/Distribuidora/public/exportarv'">Exportar a Excel</button>
        <form action="{{route("guardarLocalidad")}}" method="post">
            {{ csrf_field() }}
            @csrf
            <div class="form-group">
                <input type="text" autocomplete="nipinta" class="form-control" name="id_localidad" id="id_localidad" defaultValue="Todas" style="width:40% ;" />
                <div id="localidadlist">
                </div>
            </div>
            <button name="accionlocalidad" type="submit" class="btn btn-primary" style=" margin-top:-10px; margin-bottom:10px">Seleccionar Localidad
            </button>
    </div>
</div>
</form>
@if(session("localidad") !== null)
<h4>Localidad: {{$localidad}} <a style="margin-left:0.2%" href="{{route("ventas.indexShowTodos",["show"=>$entregadosFlag])}}">Mostrar todas las localidades</a>
</h4>
<div class="row" style="margin: 0.2%; margin-bottom:-0.4%">
    <h5 id="showEntregado">Mostrando Entregados: @if($entregadosFlag==0) No @else Si @endif</h5>
</div>
@if($entregadosFlag==0)<a class="btn btn-primary" style="margin:0.2% ;" href="{{route("ventas.indexSiShowEntregados",["localidad"=>$localidad])}}">Mostrar entregados
</a> @else
<a class="btn btn-danger" style="margin:0.2% ;" href="{{route("ventas.indexNoShowEntregados",["localidad"=>$localidad])}}">
    No mostrar entregados
</a>@endif

@endif
<div style="text-align:center" class="table-responsive">
    <table class="table table-bordered table-striped table-highlight">
        <thead>
            <tr>
                <th white-space: nowrap;>Fecha</th>
                <th>Cliente</th>
                <th>Localidad</th>
                <th>Total</th>
                <th style="width: 150px;">Pagado</th>
                <th>Diferencia</th>
                <th>Entregado</th>
                <th>Vendedor</th>
                <th>Detalles</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas->sortByDesc('created_at') as $venta)
            @if (Auth::user()->role_id=="Administrador"||Auth::user()->name==$venta->vendedor)
            @if(($venta->cliente->localidad==$localidad && ($venta->entregado != 1 || $entregadosFlag == 1)) || $localidad==='Todas' || $localidad==null)
            @if($venta->pagado==0)
            <tr style="background-color: #faa;">
                @elseif($venta->pagado==$venta->total)
            <tr style="background-color: #afa;">
                @elseif($venta->pagado>0)
            <tr style="background-color: #ff4;">
                @endif
                <td>{{$venta->created_at}}</td>
                <td>{{$venta->cliente->nombre}}</td>
                <td>{{$venta->cliente->localidad}}</td>
                <td>${{number_format($venta->total,2)}}</td>
                <td>
                    <form action="{{route('cargaPago', ['id'=>$venta->id])}}" method="post">
                        {{ csrf_field() }}
                        @csrf

                        <input type="number" step="0.1" $ required value="{{$venta->pagado}}" required class="form-control" name="pago" id="pago" placeholder=""></p>
                    </form>
                </td>

                <td>${{number_format($venta->total-$venta->pagado,2)}}</td>
                <td>
                    @if ($venta->entregado == 0)

                    <a class="btn btn-danger" href="{{route('cancelEntrega', ["id"=>$venta->id])}}">
                        <!--, ["id" => $venta->id]) -->
                        <i class="fa fa-times" aria-hidden="true"></i>

                    </a>
                    @else
                    <a class="btn btn-success" href="{{route('cargaEntrega', ["id"=>$venta->id])}}">
                        <!--  ["id" => $venta->id]) -->
                        <i class="fa fa-check-square" aria-hidden="true"></i>

                    </a>
                    @endif
                </td>
                <td><a href="{{route('totales.index', ["vendedor"=>$venta->vendedor])}}"> {{$venta->vendedor}}</a></td>
                <td>
                    <a class="btn btn-success" href="{{route("ventas.show", $venta)}}">
                        <i class="fa fa-info"></i>
                    </a>
                </td>


                <td>
                    <form action="{{route("ventas.destroy", [$venta])}}" method="post">
                        @method("delete")
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endif
            @endif
            @endforeach
        </tbody>
    </table>
</div>
</div>
</div>
<script>
    function message() {
        alert("Datos enviados!");
    }

    function showEntrega() {

        document.getElementById("showEntregado").innerHTML = "<?php $entregadosFlag = 1;
                                                                echo "Mostrar Entregados: SI";
                                                                ?>";
        document.getElementById("ventaslistado").contentWindow.location.reload(true);

        console.log("Refreshed SI");

    }

    function hideEntrega() {

        document.getElementById("showEntregado").innerHTML = "<?php $entregadosFlag = 0;
                                                                echo "Mostrar Entregados: NO";
                                                                ?>";


        console.log("Refreshed NO");

    }
</script>
<script>
    $('#id_localidad').ready(function() {

        $('#id_localidad').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token2 = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetchlocalidad') }}",
                    method: "POST",
                    data: {
                        query: query,
                        _token: _token2
                    },
                    success: function(data) {
                        $('#localidadlist').fadeIn();
                        $('#localidadlist').html(data);
                    }
                });
            }
        });

        $('#localidadlist').on('click', 'li', function() {
            $('#id_localidad').val($(this).text());
            $('#localidadlist').fadeOut();
        });

    });
</script>
@endsection