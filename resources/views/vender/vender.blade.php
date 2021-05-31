@extends("maestra")
@section("titulo", "Realizar venta")
@section("contenido")


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <div class="row">
        <div class="col-12">
            <h1>Nueva venta <i class="fa fa-cart-plus"></i></h1>
            @include("notificacion")
            
                    <div class="col-12 col-md-6">
                               <form action="{{route("editaCantidad")}}" method="post">
                @csrf
                    <div class="form-group">
                        <label for="descripcion">Producto</label>
                        <input type="text" name="codigo" autocomplete="off" id="codigo" class="form-control"required autofocus name="codigo" placeholder="Ingrese el producto" />
                        <div id="descripcionlist">
                        </div>
                    </div>
                    <div
                    <label for="existencia">Stock Disponible: </label>
                    <p id="existencia"></p>                   
                    </div>
                    {{ csrf_field() }}
                    @csrf
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" min="1" name="cantidad" autocomplete="off" id="cantidad" class="form-control"required autofocus name="cantidad" placeholder="Cantidad" />                    
                    </div>
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Agregar Producto &nbsp;
                    <i class="fa fa-plus-square fa-2x" aria-hidden="true"></i>                                        </button>
                </form>
                </div>
                </div>         


            </div>
            @if(session("productos") !== null)
                <h2>Total: ${{number_format($total, 2)}}</h2>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                <td>${{number_format($producto->precio_venta, 2)}}</td>
                                <td>{{$producto->cantidad}}</td> 
                                <td>  
                                <?php
                                $total=$producto->cantidad*$producto->precio_venta;
                                echo "$".$total."";
                                ?>
                                </td>
                                <td>
                                    <form action="{{route("quitarProductoDeVenta")}}" method="post">
                                        @method("delete")
                                        @csrf
                                        <input type="hidden" name="indice" value="{{$loop->index}}">
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
            @else
                <h2>Aquí aparecerán los productos de la venta
                    <br></h2>
            @endif
        </div>
        <div class="col-12">
                <div class="col-12 col-md-6">
                    <form action="{{route("terminarOCancelarVenta")}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="id_cliente">Cliente</label>
                            <input type="text" required class="form-control" name="id_cliente" id="id_cliente" placeholder="Ingrese el Cliente antes de finalizar la venta"/>
                            <div id="clientelist">
                        </div>
                    </div>
                    
                    {{ csrf_field() }}
                    @csrf
                            
                        @if(session("productos") !== null)
                        <div>
                            <div class="form-group">
                                <button name="accion" value="terminar" type="submit" class="btn btn-success">Terminar
                                    venta
                                </button>
                                <button name="accion" value="cancelar" type="submit" class="btn btn-danger">Cancelar
                                    venta
                                </button>
                            </div>
                        @endif
                    </form>
                    </div>
                    </div>


    </div>







    

    <script>
$('#codigo').ready(function(){

 $('#codigo').keyup(function(){ 
        var query = $(this).val();
        if(query != '')
        {
            var _token = $('input[name="_token"]').val();
         $.ajax({
        url:"{{ route('autocomplete.fetch')}}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
           $('#descripcionlist').fadeIn();  
                    $('#descripcionlist').html(data);
          }
         });     
        }
    });

    $('#descripcionlist').on('click', 'li', function(){  
        $('#codigo').val($(this).text());  
        $('#descripcionlist').fadeOut(); 
    });  


});



        $('#descripcionlist').on('click',function(){
var query = $(document.getElementById("codigo")).val();
        var _token = $('input[name="_token"]').val();
         $.ajax({
        url:"{{ route('autocomplete.fetchcantidad')}}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
            document.getElementById("existencia").textContent =data;
          }
         });
        });

</script>


<script>
$('#id_cliente').ready(function(){

 $('#id_cliente').keyup(function(){ 
        var query = $(this).val();
        if(query != '')
        {
         var _token2 = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ route('autocomplete.fetchcliente') }}",
          method:"POST",
          data:{query:query, _token:_token2},
          success:function(data){
           $('#clientelist').fadeIn();  
                    $('#clientelist').html(data);
          }
         });
        }
    });

    $('#clientelist').on('click', 'li', function(){  
        $('#id_cliente').val($(this).text());  
        $('#clientelist').fadeOut();  
    });  

});
</script>



@endsection
