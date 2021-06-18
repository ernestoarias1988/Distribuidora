<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Producto;
use App\ProductoVendido;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenderController extends Controller
{

    public function terminarOCancelarVenta(Request $request)
    {
        if ($request->input("accion") == "terminar") {
            return $this->terminarVenta($request);
        } else {
            return $this->cancelarVenta();
        }
    }

    public function guardarCliente(Request $request)
    {
        $nombre_cliente="NombreList";
        $nombre_cliente = $request->post("id_cliente");
        $cliente = Cliente::where("nombre", "=", $nombre_cliente)->first();
        if (!$cliente) {
            return redirect()
                ->route("vender.index")
                ->with("mensaje", "Cliente no encontrado");
        }else{
        session(["cliente" => $cliente,
        ]);
        return redirect()
        ->route("vender.index")
        ->with("mensaje", "Cliente Guardado");
        }
    }


    public function obtenercliente()
    {
        $productos = session("cliente");
        if (!$productos) {
            $productos = [];
        }
        return $productos;
    }

    public function terminarVenta(Request $request)
    {
        // Crear una venta
        
        $venta = new Venta(); 
        $nombre_cliente = $this->obtenercliente();        
        $cliente=$nombre_cliente;
        $lista = $cliente->lista;  
        $venta->id_cliente = $cliente->id;
        $venta->vendedor = auth()->user()->name;
        $venta->saveOrFail();
        $idVenta = $venta->id;
        $productos = $this->obtenerProductos();        
        // Recorrer carrito de compras
        foreach ($productos as $producto) {


            switch($lista)
            {
                case "1": $precio = $producto->precio_venta1;
                break;

                case "2": $precio = $producto->precio_venta2;
                break;

                case "3": $precio = $producto->precio_venta3;
                break;

                default: $precio=9999;
                break;
            }

            // El producto que se vende...
            $productoVendido = new ProductoVendido();
            $productoVendido->fill([
                "id_venta" => $idVenta,
                "descripcion" => $producto->descripcion,
                "codigo_barras" => $producto->codigo_barras,
                "precio" => $precio,
                "cantidad" => $producto->cantidad,
            ]);
            // Lo guardamos
            $productoVendido->saveOrFail();
            // Y restamos la existencia del original
            $productoActualizado = Producto::find($producto->id);
            $productoActualizado->existencia -= $productoVendido->cantidad;
            $productoActualizado->saveOrFail();
        }
        $this->vaciarProductos();
        return redirect()
            ->route("vender.index")
            ->with("mensaje", "Venta terminada");
    }

    private function obtenerProductos()
    {
        $productos = session("productos");
        if (!$productos) {
            $productos = [];
        }
        return $productos;
    }

    private function vaciarProductos()
    {
        $this->guardarProductos(null);
        session(["cliente" => null,
        ]);
        
    }

    private function guardarProductos($productos)
    {
        session(["productos" => $productos,
        ]);
    }


    public function cancelarVenta()
    {
        $this->vaciarProductos();
        return redirect()
            ->route("vender.index")
            ->with("mensaje", "Venta cancelada");
    }

    public function quitarProductoDeVenta(Request $request)
    {
        $indice = $request->post("indice");
        $productos = $this->obtenerProductos();
        array_splice($productos, $indice, 1);
        $this->guardarProductos($productos);
        return redirect()
            ->route("vender.index");
    }

    public function agregarProductoVenta(Request $request)
    {
        $codigo = $request->post("codigo");
        $producto = Producto::where("descripcion", "=", $codigo)->first();
        if (!$producto) {
            return redirect()
                ->route("vender.index")
                ->with("mensaje", "Producto no encontrado");
        }

        $lista = $request->input("lista");

        switch($lista)
        {
            case 1: $precio = $producto->precio_venta1;
            break;

            case 2: $precio = $producto->precio_venta2;
            break;

            case 3: $precio = $producto->precio_venta3;
            break;

            default: $precio = 100;
            break;
        }

        $producto->precio_venta = $precio;
        route("productos.update", [$producto]);
        $this->agregarProductoACarrito($producto);
        return redirect()
            ->route("vender.index");
    }

    private function agregarProductoACarrito($producto)
    {
        if ($producto->existencia <= 0) {
            return redirect()->route("vender.index")
                ->with([
                    "mensaje" => "No hay existencias del producto",
                    "tipo" => "danger"
                ]);
        }
        $productos = $this->obtenerProductos();
        $posibleIndice = $this->buscarIndiceDeProducto($producto->descripcion, $productos);
        // Es decir, producto no fue encontrado
        if ($posibleIndice === -1) {
            $producto->cantidad = 1;
            array_push($productos, $producto);
        } else {
            if ($productos[$posibleIndice]->cantidad + 1 > $producto->existencia) {
                return redirect()->route("vender.index")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia",
                        "tipo" => "danger"
                    ]);
            }
            $productos[$posibleIndice]->cantidad++;
        }
        $this->guardarProductos($productos);
    }

    private function buscarIndiceDeProducto(string $codigo, array &$productos)
    {
        foreach ($productos as $indice => $producto) {
            if ($producto->descripcion === $codigo) {
                return $indice;
            }
        }
        return -1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = 0;
        $cliente = $this->obtenercliente();
        foreach ($this->obtenerProductos() as $producto) {
            if($cliente)
            {
            switch($cliente->lista)
            {
                case "1": $total += $producto->cantidad * $producto->precio_venta1;
                break;

                case "2": $total += $producto->cantidad * $producto->precio_venta2;
                break;

                case "3": $total += $producto->cantidad * $producto->precio_venta3;
                break;
            }
        }else{
            
        }}        
        return view("vender.vender",
            [
                "total" => $total,
                "clientes" => Cliente::all(),
                "cliente" => $cliente
            ]);
    }

    function fetch(Request $request)
    {
     if($request->get('query'))
     {
      $query = $request->get('query');
      $data = Producto::where('descripcion', 'LIKE', "%{$query}%")
        ->get();
      $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $row)
      {
       $output .= '
       <li><a href="#">'.$row->descripcion.'</a></li>
       '
       ;
      }
      $output .= '</ul>';
      echo $output;
     }
    }


    function fetchcliente(Request $request)
    {
     if($request->get('query'))
     {
      $query = $request->get('query');
      $data = Cliente::where('nombre', 'LIKE', "%{$query}%")
        ->get();
      $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $row)
      {
       $output .= '
       <li><a href="#">'.$row->nombre.'</a></li>
       ';
      }
      $output .= '</ul>';
      echo $output;
     }
    }



    function fetchcantidad(Request $request)
    {
     if($request->get('query'))
     {
      $query = $request->get('query');
      $producto = Producto::where("descripcion", "LIKE", $query)->first();    
      if($producto)
      {
      $data = Producto::where('descripcion', 'LIKE', "%{$query}%")
        ->get();      
      foreach($data as $row)
      {
       $output = 'Stock Disponible: '.$row->existencia.'';//'     Precio: $'.$row->precio_venta1;
      }
     }else{
               $output= '';
     }
    }else{
    $output= "";
    }
    echo $output;
    }







    function editarCantidad(Request $request)
    {
        $nro=17;
        $codigo = $request->post("codigo");
        $nro = $request->post("cantidad");
        $producto = Producto::where("descripcion", "=", $codigo)->first();
        if (!$producto) {
            return redirect()
                ->route("vender.index")
                ->with("mensaje", "Producto no encontrado");
        }
        if ($producto->existencia <= 0) {
            return redirect()->route("vender.index")
                ->with([
                    "mensaje" => "No hay existencias del producto",
                    "tipo" => "danger"
                ]);
        }
        $productos = $this->obtenerProductos();
        $posibleIndice = $this->buscarIndiceDeProducto($producto->descripcion, $productos);
        // Es decir, producto no fue encontrado
        if ($posibleIndice === -1) {
            if ($producto->cantidad + $nro > $producto->existencia) {
                return redirect()->route("vender.index")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia. Stock actual: ".$producto->existencia."",
                        "tipo" => "danger"
                    ]);
            }
            $producto->cantidad = $nro;
            array_push($productos, $producto);
        } else {
            if ($productos[$posibleIndice]->cantidad + $nro > $producto->existencia) {
                return redirect()->route("vender.index")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia. Stock actual: ".$producto->existencia."",
                        "tipo" => "danger"
                    ]);
            }
            $productos[$posibleIndice]->cantidad+=$nro;
        }
        $this->guardarProductos($productos);
        return redirect()
            ->route("vender.index");
    }

}
