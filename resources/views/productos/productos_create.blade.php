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
@section("titulo", "Agregar producto")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Agregar producto</h1>
            <form method="POST" action="{{route("productos.store")}}">
                @csrf
                <div class="form-group">
                    <label class="label">Código de barras</label>
                    <input required autocomplete="off" name="codigo_barras" class="form-control"
                           type="text" placeholder="Código de barras">
                </div>
                <div class="form-group">
                    <label class="label">Descripción</label>
                    <input required autocomplete="off" name="descripcion" class="form-control"
                           type="text" placeholder="Descripción">
                </div>
                <div class="form-group">
                    <label class="label">Categoría</label>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <select required name="categoria" class="form-control">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $categoria)
                            <option value="{{$categoria->nombre}}">{{$categoria->nombre}}</option>
                            @endforeach
                        </select>
                        <a class="btn btn-outline-secondary mt-2" href="{{route("categorias.index")}}">Administrar categorías</a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label">Precio de compra</label>
                    <input required autocomplete="off" name="precio_compra" class="form-control"
                           type="decimal(9,2)" placeholder="Precio de compra">
                </div>
                <div class="form-group">
                    <label class="label">Precio de Lista 1</label>
                    <input required autocomplete="off" name="precio_venta1" class="form-control"
                           type="decimal(9,2)" placeholder="Precio de Lista 1">
                </div>
                <div class="form-group">
                    <label class="label">Precio de Lista 2</label>
                    <input required autocomplete="off" name="precio_venta2" class="form-control"
                           type="decimal(9,2)" placeholder="Precio de Lista 2">
                </div>
                <div class="form-group">
                    <label class="label">Precio de Lista 3</label>
                    <input required autocomplete="off" name="precio_venta3" class="form-control"
                           type="decimal(9,2)" placeholder="Precio de Lista 3">
                </div>
                <div class="form-group">
                    <label class="label">Existencia</label>
                    <input required autocomplete="off" name="existencia" class="form-control"
                           type="decimal(9,2)" placeholder="Existencia">
                </div>

                @include("notificacion")
                <button class="btn btn-success">Guardar</button>
                <a class="btn btn-primary" href="{{route("productos.index")}}">Volver al listado</a>
            </form>
        </div>
    </div>
@endsection
