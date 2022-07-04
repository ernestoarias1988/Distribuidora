@extends("maestra")
@section("titulo", "Productos")
@section("contenido")
<div class="row">
    <div class="col-12">
        <h1>Productos <i class="fa fa-box"></i></h1>
        <a href="{{route("productos.create")}}" class="btn btn-success mb-2">Agregar</a>
        @include("notificacion")
        <button style="text-align:center" class="btn btn-primary mb-2" onClick="window.print()">Imprimir Productos</button>
        <button style="text-align:center" class="btn btn-success mb-2" onClick="window.location.href='https://localhost/Distribuidora/public/exportarp'">Exportar a Excel</button>
        <div class="card-body">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control" style="width: fit-content; margin-bottom:0.7%">
                <button class="btn btn-success">Importar Productos</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-highlight">
                <thead>
                    <tr>
                        <th>Código de barras</th>
                        <th>Descripción</th>
                        <th>Precio de compra</th>
                        <th>Precio de Lista 1</th>
                        <th>Precio de Lista 2</th>
                        <th>Precio de Lista 3</th>
                        <th>Utilidad</th>
                        <th>Existencia</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td>{{$producto->codigo_barras}}</td>
                        <td>{{$producto->descripcion}}</td>
                        <td>${{$producto->precio_compra}}</td>
                        <td>${{$producto->precio_venta1}}</td>
                        <td>${{$producto->precio_venta2}}</td>
                        <td>${{$producto->precio_venta3}}</td>
                        <td>${{$producto->precio_venta1 - $producto->precio_compra}}</td>
                        <td>@if($producto->existencia>0) {{$producto->existencia}}
                            @else
                            <span style="color: #f00;text-align:center; font-weight: bold;"> {{$producto->existencia}} </span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-warning" href="{{route("productos.edit",[$producto])}}">
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <form action="{{route("productos.destroy", [$producto])}}" method="post">
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