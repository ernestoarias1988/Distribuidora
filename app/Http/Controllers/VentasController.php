<?php

namespace App\Http\Controllers;

use App\Venta;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Barryvdh\DomPDF\Facade as PDF;
use FontLib\Table\Type\post;
use App\ProductoVendido;
use App\Producto;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class VentasExport implements FromCollection, WithStrictNullComparison, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Nro Venta',
            'Pagado',
            'Entregado',
            'Vendedor',
            'Fecha Venta',
            'Fecha Modificada',
            'Id Cliente',
            'Cliente',
            'Total Venta'
        ];
    }
    public function collection()
    {

        $totales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->Join('clientes', 'clientes.id', '=', 'ventas.id_cliente')
            ->select("ventas.*", "clientes.nombre", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor", "clientes.nombre")
            ->get();


        return $totales;
    }
}
class VentasController extends Controller
{

    public function export()
    {
        return Excel::download(new VentasExport, 'Ventas.xlsx');
    }


    public function ticket(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $nombreImpresora = env("NOMBRE_IMPRESORA");
        $connector = new WindowsPrintConnector($nombreImpresora);
        $impresora = new Printer($connector);
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setEmphasis(true);
        $impresora->text("Ticket de venta\n");
        $impresora->text($venta->created_at . "\n");
        $impresora->setEmphasis(false);
        $impresora->text("Cliente: ");
        $impresora->text($venta->cliente->nombre . "\n");
        $impresora->text("\n===============================\n");
        $total = 0;
        foreach ($venta->productos as $producto) {
            $subtotal = $producto->cantidad * $producto->precio;
            $total += $subtotal;
            $impresora->setJustification(Printer::JUSTIFY_LEFT);
            $impresora->text(sprintf("%.2fx%s\n", $producto->cantidad, $producto->descripcion));
            $impresora->setJustification(Printer::JUSTIFY_RIGHT);
            $impresora->text('$' . number_format($subtotal, 2) . "\n");
        }
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->text("\n===============================\n");
        $impresora->setJustification(Printer::JUSTIFY_RIGHT);
        $impresora->setEmphasis(true);
        $impresora->text("Total: $" . number_format($total, 2) . "\n");
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(1, 1);
        $impresora->text("Gracias por su compra\n");
        $impresora->feed(5);
        $impresora->close();
        return redirect()->back()->with("mensaje", "Ticket impreso");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localidad = $this->obtenerlocalidad();
        $entregadosFlag = 0;
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor")
            ->get();
        return view("ventas.ventas_index", [
            "ventas" => $ventasConTotales, "localidad" => $localidad, "entregadosFlag" => $entregadosFlag
        ]);
    }

    public function indexNoShowEntregados()
    {
        $localidad = $this->obtenerlocalidad();
        $entregadosFlag = 0;
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor")
            ->get();
        return view("ventas.ventas_index", [
            "ventas" => $ventasConTotales, "localidad" => $localidad, "entregadosFlag" => $entregadosFlag
        ]);
    }

    public function indexSiShowEntregados()
    {
        $localidad = $this->obtenerlocalidad();
        $entregadosFlag = 1;
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor")
            ->get();
        return view("ventas.ventas_index", [
            "ventas" => $ventasConTotales, "localidad" => $localidad, "entregadosFlag" => $entregadosFlag
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Venta $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta)
    {
        $total = 0;
        foreach ($venta->productos as $producto) {
            $total += $producto->cantidad * $producto->precio;
        }
        return view("ventas.ventas_show", [
            "venta" => $venta,
            "total" => $total,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Venta $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Venta $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Venta $venta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta)
    {
        $productos = $venta->productos;
        // Recorrer carrito de compras
        foreach ($productos as $producto) {
            /*/ El producto que se vende...
            $productoVendido = new ProductoVendido();
            $productoVendido->fill([
                "id_venta" => $venta->id,
                "descripcion" => $producto->descripcion,
                "codigo_barras" => $producto->codigo_barras,
                "precio" => $producto->precio_venta,
                "cantidad" => $producto->cantidad,
            ]);
            */ // Lo guardamos
            //$producto->saveOrFail();
            // Y restamos la existencia del original
            //$productoActualizado = Producto::find($producto->id);
            $productoActualizado = Producto::where("descripcion", "=", $producto->descripcion)->first();
            //echo"$productoActualizado->id";
            echo "$producto->cantidad";
            echo "$producto->descripcion";
            echo "---";
            $productoActualizado->existencia += $producto->cantidad;
            $productoActualizado->saveOrFail();
        }
        $venta->delete();
        return redirect()->route("ventas.index")
            ->with("mensaje", "Venta eliminada");
    }

    public function destroyProducto(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $descripcion = $request->get("descripcion");
        $productos = $venta->productos;
        // Recorrer carrito de compras
        foreach ($productos as $producto) {
            /*/ El producto que se vende...
            $productoVendido = new ProductoVendido();
            $productoVendido->fill([
                "id_venta" => $venta->id,
                "descripcion" => $producto->descripcion,
                "codigo_barras" => $producto->codigo_barras,
                "precio" => $producto->precio_venta,
                "cantidad" => $producto->cantidad,
            ]);
            */ // Lo guardamos
            //$producto->saveOrFail();
            // Y restamos la existencia del original
            //$productoActualizado = Producto::find($producto->id);
            if ($producto->descripcion == $descripcion) {
                $productoActualizado = Producto::where("descripcion", "=", $producto->descripcion)->first();
                echo "$producto->descripcion == $descripcion <br>";
                echo "$producto->cantidad";
                echo "$venta->id";
                echo "$productoActualizado->descripcion";
                $productoActualizado->existencia += $producto->cantidad;
                $productoActualizado->saveOrFail();
                $producto->cantidad = 0;
                $producto->delete();
            }
        }
        //  $venta->delete();
        return redirect()->route("ventas.index")
            ->with("mensaje", "Producto $producto->descrpcion eliminado");
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getData($request);
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('pdf.comprobante', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }



    public function getData(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));

        $data =  [
            'facturaNro'  => $venta->id,
            'cliente'   => $venta->cliente->nombre,
            'Request' => $request,
            'vendedor' => $venta->vendedor,
            'descuento' => 0,
            'direccion' => $venta->cliente->direccion
        ];
        return $data;
    }
    public function exportVentasPdf(Request $request)
    {
        $data = [
            "ventas" => Venta::all(),
            "localidad" => $request->get("id")
        ];
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('pdf.ventas', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }
    public function getDataVentas(Request $request)
    {
        /*$venta = Venta::findOrFail($request->get("id"));
        $data =  [
            'facturaNro'  => $venta->id,
            'cliente'   => $venta->cliente->nombre,
            'Request' => $request,
            'vendedor' => $venta->vendedor,
            'descuento' => 0,
            'direccion' => $venta->cliente->direccion
        ];*/
        $data = ["localidad" => $request->get("id")];
        return $data;
    }
    public function cancelarPago(Request $request)
    {

        $venta = Venta::findOrFail($request->get("id"));
        $venta->pagado = 1;
        $venta->save();
        return redirect()->route("ventas.index")->with("mensaje", "Venta Pagada");
    }

    public function cancelarEntrega(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $venta->entregado = 1;
        $venta->save();
        return redirect()->route("ventas.index")->with("mensaje", "Venta Entregada");
    }

    public function cargarPago(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $pago = $request->get("pago");
        $venta->pagado = $pago;
        $venta->save();
        return redirect()->route("ventas.index")->with("mensaje", "Venta Actualizada");
    }

    public function cargarEntrega(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $venta->entregado = 0;
        $venta->save();
        return redirect()->route("ventas.index")->with("mensaje", "Venta NO Entregada");
    }

    public function cargarCantidad(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $descripcion = $request->get("descripcion");
        $cantidad = $request->get("cantidad");
        $productos = $venta->productos;
        // Recorrer carrito de compras
        foreach ($productos as $producto) {
            if ($producto->descripcion == $descripcion) {
                $productoActualizado = Producto::where("descripcion", "=", $producto->descripcion)->first();
                $diferencia = $producto->cantidad - $cantidad;
                /*if(($diferencia*-1) > $productoActualizado->existencia)
                        {
                            return redirect()->route("ventas.index")->with("mensaje", "No hay Stock suficiente");
                        }   */
                #TODO: Que no sea necesario apretar enter para cargar la cantidad             
                echo "$producto->descripcion == $descripcion <br>";
                echo "$producto->cantidad";
                echo "$venta->id";
                echo "$productoActualizado->descripcion";
                $productoActualizado->existencia += $diferencia;
                $productoActualizado->saveOrFail();
                $producto->cantidad = $cantidad;
                if ($cantidad == 0) {
                    $producto->delete();
                } else {
                    $producto->save();
                    $venta->save();
                }
            }
        }
        return redirect()->route("ventas.index")->with("mensaje", "Venta Actualizada");
    }

    function fetchlocalidad(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = Cliente::where('localidad', 'LIKE', "%{$query}%")
                ->get();
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '
       <li><a href="#">' . $row->localidad . '</a></li>
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function guardarLocalidad(Request $request)
    {
        $localidad_cliente = "NombreList";
        $localidad_cliente = $request->post("id_localidad");
        $cliente = Cliente::where("localidad", 'LIKE', $localidad_cliente)->first();
        if (!$cliente) {
            session([
                "localidad" => 'Todas'
            ]);
            return redirect()
                ->route("ventas.index")
                ->with("mensaje", "Localidad no encontrada");
        } else {
            session([
                "localidad" => $cliente->localidad,
            ]);
            return redirect()
                ->route("ventas.index")
                ->with("mensaje", "Localidad Guardada:$cliente->localidad");
        }
    }


    public function obtenerlocalidad()
    {
        $localidad = session("localidad");
        if (!$localidad || $localidad == 'Todas') {
            $localidad = 'Todas';
        }
        return $localidad;
    }



    public function cargarCantidadShow(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $descripcion = $request->get("descripcion");
        $cantidad = $request->get("cantidad");
        $productos = $venta->productos;
        // Recorrer carrito de compras
        foreach ($productos as $producto) {
            if ($producto->descripcion == $descripcion) {
                $productoActualizado = Producto::where("descripcion", "=", $producto->descripcion)->first();
                $diferencia = $producto->cantidad - $cantidad;
                /*if(($diferencia*-1) > $productoActualizado->existencia)
                        {
                            return redirect()->route("ventas.index")->with("mensaje", "No hay Stock suficiente");
                        }   */
                #TODO: Que no sea necesario apretar enter para cargar la cantidad             
                echo "$producto->descripcion == $descripcion <br>";
                echo "$producto->cantidad";
                echo "$venta->id";
                echo "$productoActualizado->descripcion";
                $productoActualizado->existencia += $diferencia;
                $productoActualizado->saveOrFail();
                $producto->cantidad = $cantidad;
                if ($cantidad == 0) {
                    $producto->delete();
                } else {
                    $producto->save();
                    $venta->save();
                }
            }
        }
        return redirect()->route("ventas.show", $venta)->with("mensaje", "Venta Actualizada");
    }
}
