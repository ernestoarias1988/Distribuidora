<?php

namespace App\Http\Controllers;

use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Barryvdh\DomPDF\Facade as PDF;
use FontLib\Table\Type\post;

class VentasController extends Controller
{

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
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor")
            ->get();
        return view("ventas.ventas_index", ["ventas" => $ventasConTotales,]);
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
        $venta->delete();
        return redirect()->route("ventas.index")
            ->with("mensaje", "Venta eliminada");
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
            'descuento'=> 0,
            'direccion'=>$venta->cliente->direccion        
        ];
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
        $venta->pagado = 0;
        $venta->save();
        return redirect()->route("ventas.index")->with("mensaje", "Venta NO Pagada");
                
    }

    public function cargarEntrega(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $venta->entregado = 0;
        $venta->save();        
        return redirect()->route("ventas.index")->with("mensaje", "Venta NO Entregada");

    }
}

