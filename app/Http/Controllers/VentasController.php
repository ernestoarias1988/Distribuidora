<?php

namespace App\Http\Controllers;

use App\Venta;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;
use FontLib\Table\Type\post;
use App\ProductoVendido;
use App\Producto;
use App\User;
use Illuminate\Validation\Rules\Exists;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        return view("ventas.ventas_index", $this->buildVentasIndexData($request));
    }

    public function indexNoShowEntregados(Request $request)
    {
        return redirect()->route("ventas.index", [
            "localidad" => $request->get("localidad", $this->obtenerlocalidad()),
            "entregados" => 0,
            "periodo" => $this->sanitizePeriodo($request->get("periodo", "mes")),
        ]);
    }

    public function indexSiShowEntregados(Request $request)
    {
        return redirect()->route("ventas.index", [
            "localidad" => $request->get("localidad", $this->obtenerlocalidad()),
            "entregados" => 1,
            "periodo" => $this->sanitizePeriodo($request->get("periodo", "mes")),
        ]);
    }
    public function acumulados(Request $request)
    {
        $vendedores = User::all();
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor","ventas.idApp")
            ->get();
        return view("totales.acumulados", [
            "ventas" => $ventasConTotales, "localidad" => 'Todas', "vendedores" => $vendedores
        ]);
    }
    public function indexShowTodos(Request $request)
    {
        return redirect()->route("ventas.index", [
            "localidad" => "Todas",
            "entregados" => (int) $request->get("show", 0),
            "periodo" => $this->sanitizePeriodo($request->get("periodo", "mes")),
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

        $codigosBarras = $venta->productos
            ->pluck('codigo_barras')
            ->filter()
            ->unique()
            ->values();

        $stockPorCodigo = Producto::whereIn('codigo_barras', $codigosBarras)
            ->pluck('existencia', 'codigo_barras')
            ->toArray();

        return view("ventas.ventas_show", [
            "venta" => $venta,
            "total" => $total,
            "stockPorCodigo" => $stockPorCodigo,
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
            /* echo "$producto->cantidad";
            echo "$producto->descripcion";
            echo "---";*/
            if ($productoActualizado != null) {
                $productoActualizado->existencia += $producto->cantidad;
                $productoActualizado->saveOrFail();
            }
        }
        $usuario = Auth::user()->role_id;
        $message = $usuario.' Esta eliminando Venta de' ;
        Log::debug($message.' '.$venta->vendedor.' La venta era: '.$venta);
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
        $localidad = $request->get("id", $request->get("localidad", $this->obtenerlocalidad()));
        $periodo = $this->sanitizePeriodo($request->get("periodo", "mes"));
        $entregadosFlag = (int) $request->get("entregados", 0);

        $data = [
            "ventas" => $this->getVentasFiltradas($localidad, $periodo, $entregadosFlag),
            "localidad" => $localidad,
            "periodo" => $periodo,
        ];
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('pdf.ventas', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }
    public function exportVentasVendedorPdf(Request $request)
    {
        $vendedor = $request->get("id");
        $localidad = $request->get("localidad");
        $ventas = Venta::where('vendedor', $vendedor)
            ->where(function($query) use ($localidad) {
                if ($localidad !== 'Todas' && $localidad !== null) {
                    $query->whereHas('cliente', function($q) use ($localidad) {
                        $q->where('localidad', $localidad);
                    });
                }
            })
            ->where('entregado', '!=', 1)
            ->get()
            ->filter(function($venta) {
                return $venta->productos && $venta->productos->count() > 0;
            });
        $data = [
            "ventas" => $ventas,
            "vendedor" => $vendedor,
            "localidad" => $localidad
        ];
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('pdf.ventasVendedor', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('letter', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }

    public function exportAcumuladoVendedorPdf(Request $request)
    {
        $data = [
            "ventas" => Venta::all(),
            "vendedor" => $request->get("id"),
            "localidad" => $request->get("localidad")
        ];
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('pdf.acumuladoVendedor', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('letter', 'landscape');
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
        return redirect()->route("ventas.index", $this->ventasIndexRedirectParams($request))->with("mensaje", "Venta Pagada");
    }

    public function cancelarEntrega(Request $request)
    {
        error_log('Cancelada');
        $venta = Venta::findOrFail($request->get("id"));
        $venta->entregado = 0;
        $venta->save();
        return redirect()->route("ventas.index", $this->ventasIndexRedirectParams($request))->with("mensaje", "Venta No Entregada");
    }

    public function cargarPago(Request $request)
    {
        $venta = Venta::findOrFail($request->get("id"));
        $pago = $request->get("pago");
        $venta->pagado = $pago;
        $venta->save();
        return redirect()->route("ventas.index", $this->ventasIndexRedirectParams($request))->with("mensaje", "Venta Actualizada");
    }

    public function cargarEntrega(Request $request)
    {
        error_log('Hello');
        $venta = Venta::findOrFail($request->get("id"));
        $venta->entregado = 1;
        $venta->save();
        return redirect()->route("ventas.index", $this->ventasIndexRedirectParams($request))->with("mensaje", "Venta Entregada");
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
    public function fetchlocalidad(Request $request)
    {
        $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
        $localidades = [];
    
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = Cliente::where('localidad', 'LIKE', "%{$query}%")->get();
    
            if ($data->isNotEmpty()) {
                foreach ($data as $row) {
                    $localidades[] = $row->localidad;
                }
                $localidades = array_unique($localidades);
                foreach ($localidades as $row) {
                    $output .= '<li><a href="#">' . $row . '</a></li>';
                }
            }
        }
    
        $output .= '</ul>';
        return response()->json(['output' => $output, 'localidades' => $localidades]);
    }

    public function guardarLocalidad(Request $request)
    {
        $localidad_cliente = "NombreList";
        $localidad_cliente = $request->post("id_localidad");
        $filtros = [
            "periodo" => $this->sanitizePeriodo($request->post("periodo", "mes")),
            "entregados" => (int) $request->post("entregados", 0),
        ];

        $cliente = Cliente::where("localidad", 'LIKE', $localidad_cliente)->first();
        if (!$cliente) {
            session([
                "localidad" => 'Todas'
            ]);
            if($localidad_cliente == "Todas")
            {
                return redirect()
                ->route("ventas.index", array_merge($filtros, ["localidad" => "Todas"]));
            }
            return redirect()
                ->route("ventas.index", $filtros)
                ->with("mensaje", "Localidad no encontrada");
        } else {
            session([
                "localidad" => $cliente->localidad,
            ]);
            return redirect()
                ->route("ventas.index", array_merge($filtros, ["localidad" => $cliente->localidad]));
               // ->with("mensaje", "Localidad Guardada:$cliente->localidad");
        }
    }

    private function buildVentasIndexData(Request $request)
    {
        $localidad = $request->query("localidad", $this->obtenerlocalidad());
        $periodo = $this->sanitizePeriodo($request->query("periodo", "mes"));
        $entregadosFlag = (int) $request->query("entregados", 0);
        $periodoRango = $this->resolvePeriodoRango($periodo);

        $ventasConTotales = $this->getVentasFiltradas($localidad, $periodo, $entregadosFlag);

        return [
            "ventas" => $ventasConTotales,
            "localidad" => $localidad,
            "entregadosFlag" => $entregadosFlag,
            "periodo" => $periodo,
            "periodoLabel" => $periodoRango["label"],
        ];
    }

    private function getVentasFiltradas($localidad, $periodo, $entregadosFlag)
    {
        $periodoRango = $this->resolvePeriodoRango($periodo);

        return Venta::with(["cliente", "productos"])
            ->join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
            ->where("ventas.created_at", ">", "2023-10-16 11:15:35")
            ->whereBetween("ventas.created_at", [$periodoRango["inicio"], $periodoRango["fin"]])
            ->when(Auth::user()->role_id != "Administrador", function ($query) {
                $query->where("ventas.vendedor", Auth::user()->email);
            })
            ->when($localidad && $localidad !== "Todas", function ($query) use ($localidad) {
                $query->whereHas("cliente", function ($clienteQuery) use ($localidad) {
                    $clienteQuery->where("localidad", $localidad);
                });
            })
            ->when($entregadosFlag !== 1, function ($query) {
                $query->where("ventas.entregado", "!=", 1);
            })
            ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
            ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at", "ventas.id_cliente", "ventas.vendedor", "ventas.idApp")
            ->orderBy("ventas.created_at", "desc")
            ->get();
    }

    private function sanitizePeriodo($periodo)
    {
        $periodosValidos = ["semana", "mes", "anio"];

        if (!in_array($periodo, $periodosValidos, true)) {
            return "mes";
        }

        return $periodo;
    }

    private function resolvePeriodoRango($periodo)
    {
        $ahora = Carbon::now();

        if ($periodo === "semana") {
            return [
                "inicio" => $ahora->copy()->startOfWeek(Carbon::MONDAY),
                "fin" => $ahora->copy()->endOfWeek(Carbon::SUNDAY),
                "label" => "Semana actual",
            ];
        }

        if ($periodo === "anio") {
            return [
                "inicio" => $ahora->copy()->startOfYear(),
                "fin" => $ahora->copy()->endOfYear(),
                "label" => "Año actual",
            ];
        }

        return [
            "inicio" => $ahora->copy()->startOfMonth(),
            "fin" => $ahora->copy()->endOfMonth(),
            "label" => "Mes actual",
        ];
    }

    private function ventasIndexRedirectParams(Request $request)
    {
        return [
            "localidad" => $request->get("localidad", $this->obtenerlocalidad()),
            "periodo" => $this->sanitizePeriodo($request->get("periodo", "mes")),
            "entregados" => (int) $request->get("entregados", 0),
        ];
    }


    public function obtenerlocalidad()
    {
        $localidad = session("localidad");
        if (!$localidad || $localidad == 'Todas') {
            $localidad = 'Todas';
        }
        return $localidad;
    }

    public function showLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = file_exists($logFile) ? file_get_contents($logFile) : '';
        return view('logs.show', compact('logs'));
    }

    public function archiveLog()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $newName = storage_path('logs/logs_' . date('Y-m-d_H-i-s') . '.txt');
            rename($logFile, $newName);
        }
        return redirect()->route('logs.show')->with('status', 'Log archived successfully.');
    }


    public function showPolicy()
    {
        return view('policy.show');
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
