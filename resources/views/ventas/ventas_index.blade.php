@extends("maestra")
@section("titulo", "Ventas")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Ventas <i class="fa fa-list"></i></h1>
            @include("notificacion")
            <button style="text-align:center" class="btn btn-primary mb-2" onClick="window.print()">Imprimir Ventas</button> 
            <button style="text-align:center" class="btn btn-success mb-2" onClick="window.location.href='https://distribuidora.tantunapps.com/public//exportarv'">Exportar a Excel</button>        
            <div  style="text-align:center" class="table-responsive">
            <table class="table table-bordered table-striped table-highlight">
                    <thead>
                    <tr>
                        <th white-space: nowrap;>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Entregado</th>
                        <th style="width: 100px;">Pago</th>
                        <th>Vendedor</th>
                        <th>Detalles</th>
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
                                @if ($venta->entregado == 0)
  
                                <a class="btn btn-danger" href="{{route('cancelEntrega', ["id"=>$venta->id])}}">  <!--, ["id" => $venta->id]) -->
                                <i class="fa fa-times" aria-hidden="true"></i>

                                </a>                                                                                             
                                @else
                                <a class="btn btn-success" href="{{route('cargaEntrega', ["id"=>$venta->id])}}"> <!--  ["id" => $venta->id]) -->
                                <i class="fa fa-check-square" aria-hidden="true"></i>

                                </a>                                                            
                                @endif                                                             
                            </td>
                            <td style="width: 100px;">
                                <form action="{{route('cargaPago', ['id'=>$venta->id])}}" method="post">
                                {{ csrf_field() }}
                                 @csrf
                                <div>
                                <input  type="number" autocomplete="off" required class="form-control" name="pago" id="pago" placeholder="$"/>   
                                </div>
                                <button name="accionpago" type="submit" class="btn btn-primary">$                                    
                                </button>
                                </div>
                                </div>
                                </form>
                                @if ($venta->pagado == 0)
                                <a class="btn btn-danger" href="">
                                <i class="fa fa-times" aria-hidden="true"></i>
                                </a>
                                </td>                                                             
                                @endif
                                @if(($venta->pagado == $venta->total))
                                <a class="btn btn-success" href="">
                                    <i class="fa fa-check-square" aria-hidden="true"></i>
                                </a>                                 
                                @else
                                @if($venta->pagado>0)
                                ${{$venta->pagado}}/${{number_format($venta->total, 0)}}                                 
                                @endif
                                @endif
                                </td>                                                             
                                </td>  
                                                                                           
                                <td>{{$venta->vendedor}}</td>
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
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
