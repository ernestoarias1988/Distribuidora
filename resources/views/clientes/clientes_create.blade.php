@extends("maestra")
@section("titulo", "Agregar cliente")
@section("contenido")


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div class="row">
    <div class="col-12">
        <h1>Agregar cliente</h1>
        <form method="POST" action="{{route("clientes.store")}}">
            @csrf
            <div class="form-group">
                <label class="label">Nombre</label>
                <input required autocomplete="off" name="nombre" class="form-control" type="text" placeholder="Nombre">
            </div>
            <div class="form-group">
                <label class="label">Teléfono</label>
                <input required autocomplete="off" name="telefono" class="form-control" type="number" placeholder="Teléfono">
            </div>
            <div class="form-group">
                <label class="label">Localidad</label>
                <select name="localidad" id="localidad" class="form-control @error('lista') is-invalid @enderror" required autocomplete="localidad" autofocus>
                    <option value="" selected disabled hidden>Seleccione Localidad</option>
                    <option value="Salta">Salta</option>
                    <option value="Pichanal">Pichanal</option>
                    <option value="Orán">Orán</option>
                    <option value="Embarcacion/Irigoyen">Embarcacion/Irigoyen</option>
                    <option value="Chaco">Chaco</option>
                    <option value="Morillo">Morillo</option>
                    <option value="Colonia Santa Rosa">Colonia Santa Rosa</option>
                    <option value="Otra">Otra</option>

                </select>
            </div>
            <div class="form-group">
                <label class="label">Dirección</label>
                <input required autocomplete="off" name="direccion" class="form-control" type="text" placeholder="Dirección">
            </div>
            <div class="form-group">
                <label class="label">Lista</label>
                <select name="lista" id="lista" class="form-control @error('lista') is-invalid @enderror" required autocomplete="lista" autofocus>
                    <option value="1">Lista 1</option>
                    <option value="2">Lista 2</option>
                    <option value="3">Lista 3</option>
                </select>
            </div>
            {{ csrf_field() }}
            @csrf
            <div class="form-group">
                <label for="vendedor">Vendedor</label>
                <input type="text" autocomplete="off" required class="form-control" name="vendedor" id="vendedor" placeholder="Nombre del Vendedor" />
                <div id="vendedorList">
                </div>
            </div>
            @include("notificacion")
            <button class="btn btn-success">Guardar</button>
            <a class="btn btn-primary" href="{{route("clientes.index")}}">Volver al listado</a>
        </form>
    </div>
</div>



<script>
    $('#vendedor').ready(function() {

        $('#vendedor').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token2 = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.fetchvendedor') }}",
                    method: "POST",
                    data: {
                        query: query,
                        _token: _token2
                    },
                    success: function(data) {
                        $('#vendedor').fadeIn();
                        $('#vendedorList').html(data);
                    }
                });
            }
        });

        $('#vendedorList').on('click', 'li', function() {
            $('#vendedor').val($(this).text());
            $('#vendedorList').fadeOut();
        });

    });
</script>

@endsection