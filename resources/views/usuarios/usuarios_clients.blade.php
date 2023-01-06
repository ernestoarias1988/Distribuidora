@extends("maestra")
@section("titulo", "Acumulado")
@section("contenido")

<h1>Vendedor: {{$usuario->name}}</h1>

@foreach($clientes as $cliente)

@if($cliente->vendedor==$usuario->name)
<strong>Cliente:</strong> {{$cliente->nombre}}<br>
<strong>Localidad:</strong> {{$cliente->localidad}}<br>
<strong>Direccion:</strong> {{$cliente->direccion}}<br>
<strong>Telefono:</strong> {{$cliente->telefono}}<br>
-------------<br>
@endif
@endforeach


@endsection