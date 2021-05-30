
@extends('maestra')
@section("titulo", "Inicio")
@section('contenido')

    <div class="col-12 text-center">
        <h1>Bienvenido, {{Auth::user()->name}}</h1>
        @if (Auth::user()->role_id=="1")
        <h1>Rol: Administrador</h1>
        @endif
    </div>
    @if (Auth::user()->role_id=="Administrador")
    @foreach([
    ["productos", "vender","ventas", "clientes"],
    ["usuarios"]
    ] as $modulos)
        <div class="col-10 pb-2">
            <div class="row">
                @foreach($modulos as $modulo)
                <div class="col-10 col-md-3">
                        <div class="card">
                            <img style="max-width:100%;width:auto;height:auto;" class="card-img-top" src="{{url("/img/$modulo.png")}}">
                            <div class="card-body">
                                <a href="{{route("$modulo.index")}}" class="btn btn-success">
                                    Ir a&nbsp;{{$modulo === "acerca_de" ? "Acerca de" : ucwords($modulo)}}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    @endif
  

    @if (Auth::user()->role_id=="Vendedor")
    @foreach([
    ["productos", "ventas", "vender", "clientes"]
    ] as $modulos)
        <div class="col-10 pb-2">
            <div class="row">
                @foreach($modulos as $modulo)
                    <div class="col-10 col-md-3">
                        <div class="card">
                            <img style="max-width:100%;width:auto;height:auto;" class="card-img-top" src="{{url("/img/$modulo.png")}}">
                            <div class="card-body">
                                <a href="{{route("$modulo.index")}}" class="btn btn-success">
                                    Ir a&nbsp;{{$modulo === "acerca_de" ? "Acerca de" : ucwords($modulo)}}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    @endif

    @if (Auth::user()->role_id=="Repartidor")
    @foreach([
    ["ventas"]
    ] as $modulos)
        <div class="col-10 pb-2">
            <div class="row">
                @foreach($modulos as $modulo)
                    <div class="col-10 col-md-3">
                        <div class="card">
                            <img style="max-width:100%;width:auto;height:auto;" class="card-img-top" src="{{url("/img/$modulo.png")}}">
                            <div class="card-body">
                                <a href="{{route("$modulo.index")}}" class="btn btn-success">
                                    Ir a&nbsp;{{$modulo === "acerca_de" ? "Acerca de" : ucwords($modulo)}}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    @endif
   
@endsection
