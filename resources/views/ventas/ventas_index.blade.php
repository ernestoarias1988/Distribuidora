{{--

  ____          _____               _ _           _
 |  _ \        |  __ \             (_) |         | |
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |
        |___/                               |___/

    Blog:       https://parzibyte.me/blog
    Ayuda:      https://parzibyte.me/blog/contrataciones-ayuda/
    Contacto:   https://parzibyte.me/blog/contacto/

    Copyright (c) 2020 Luis Cabrera Benito
    Licenciado bajo la licencia MIT

    El texto de arriba debe ser incluido en cualquier redistribucion
--}}
@extends("maestra")
@section("titulo", "Ventas")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Ventas <i class="fa fa-list"></i></h1>
            @include("notificacion")
            <button style="text-align:center" class="btn btn-success" onClick="window.print()">Imprimir Ventas</button>        
            <div  style="text-align:center" class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th white-space: nowrap;>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Comprobante</th>
                        <th>Detalles</th>
                        <th>Entregado</th>
                        <th>Pagado</th>
                        <th>Vendedor</th>
                        <th>Eliminar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ventas->sortByDesc('created_at') as $venta)
                    
                    
                        <tr>
                            <td>{{$venta->created_at}}</td>
                            <td>{{$venta->cliente->nombre}}</td>
                            <td>${{number_format($venta->total, 2)}}</td>
                            <td>
                                <a class="btn btn-info" target="blank" href="{{route("users.pdf", ["id"=>$venta->id])}}">  <!--, ["id" => $venta->id]) -->
                                    <i class="fa fa-print"></i>
                                </a>
                            </td>

                            <td>
                                <a class="btn btn-success" href="{{route("ventas.show", $venta)}}">
                                    <i class="fa fa-info"></i>
                                </a>                                                               
                                
                                @if ($venta->entregado == 0)
                                <td>
                                <a class="btn btn-danger" href="{{route('cancelEntrega', ["id"=>$venta->id])}}">  <!--, ["id" => $venta->id]) -->
                                <i class="fa fa-times" aria-hidden="true"></i>

                                </a>
                                </td>                                                             
                                @else
                                <td>
                                <a class="btn btn-success" href="{{route('cargaEntrega', ["id"=>$venta->id])}}"> <!--  ["id" => $venta->id]) -->
                                <i class="fa fa-check-square" aria-hidden="true"></i>

                                </a>
                                </td>                                                             
                                @endif
                                </td>                                                             
                                
                                @if ($venta->pagado == 0)
                                <td>
                                <a class="btn btn-danger" href="{{route('cancelPago', ["id"=>$venta->id])}}" -->  <!--, ["id" => $venta->id]) -->
                                <i class="fa fa-times" aria-hidden="true"></i>

                                </a>
                                </td>                                                             
                                @else
                                <td>
                                <a class="btn btn-success" href="{{route("cargaPago", ["id"=>$venta->id])}}" >  <!--, ["id" => $venta->id]) -->
                                    <i class="fa fa-check-square" aria-hidden="true"></i>

                                </a> 

                                </td>                                                             
                                @endif
                                </td>                                                             
                                <td>{{$venta->vendedor}}</td>
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
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
