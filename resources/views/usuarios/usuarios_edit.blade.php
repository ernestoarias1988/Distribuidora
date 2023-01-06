@extends("maestra")
@section("titulo", "Editar usuario")
@section("contenido")
<div class="row">
    <div class="col-12">
        <h1>Editar usuario</h1>
        <form method="POST" action="{{route("usuarios.update", [$usuario])}}">
            @method("PUT")
            @csrf
            <div class="form-group">
                <label class="label">Nombre</label>
                <input required value="{{$usuario->name}}" autocomplete="off" name="name" class="form-control" type="text" placeholder="Nombre">
            </div>
            <div class="form-group">
                <label class="label">Usuario</label>
                <input required value="{{$usuario->email}}" autocomplete="off" name="email" class="form-control" type="text" placeholder="Usuario">
            </div>
            <div class="form-group">
                <label class="label">Rol</label>

                <select name="role_id" value="{{$usuario->role_id}}" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required autocomplete="role_id" autofocus>
                    <option value="{{$usuario->role_id}}" selected disabled hidden>{{$usuario->role_id}}</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Vendedor">Vendedor</option>
                    <option value="Repartidor">Repartidor</option>
                </select>
            </div>
            <div class="form-group">
                <label class="label">Contraseña</label>
                <input required value="{{$usuario->password}}" autocomplete="off" name="password" class="form-control" type="password" placeholder="Contraseña">
            </div>

            @include("notificacion")
            <button class="btn btn-success">Guardar</button>
            <a class="btn btn-primary" href="{{route("usuarios.index")}}">Volver</a>
        </form>
    </div>
</div>
@endsection