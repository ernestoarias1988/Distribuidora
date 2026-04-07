<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function esAdmin($user)
    {
        return (string) $user->role_id === "Administrador" || (string) $user->role_id === "1";
    }

    public function index(Request $request)
    {
        if (!$this->esAdmin(auth()->user())) {
            abort(403, "No autorizado");
        }

        $tiposReporte = [
            "productos_mas_menos" => "Productos mas y menos vendidos",
            "cantidad_vendida" => "Ingresos por vendedor o localidad",
        ];

        $tipoReporte = $request->get("tipo_reporte");
        $vendedor = $request->get("vendedor");
        $localidad = $request->get("localidad");
        $agruparPor = $request->get("agrupar_por", "vendedor");
        $topN = (int) $request->get("top_n", 8);
        if ($topN < 3) {
            $topN = 3;
        }
        if ($topN > 20) {
            $topN = 20;
        }
        $periodos = [30, 90, 180, 360];
        $reportes = [];
        $periodosCantidad = [30, 180, 360];
        $reportesCantidad = [];

        $vendedores = DB::table("ventas")
            ->select("vendedor")
            ->whereNotNull("vendedor")
            ->where("vendedor", "!=", "")
            ->distinct()
            ->orderBy("vendedor")
            ->pluck("vendedor");

        $localidades = DB::table("clientes")
            ->select("localidad")
            ->whereNotNull("localidad")
            ->where("localidad", "!=", "")
            ->distinct()
            ->orderBy("localidad")
            ->pluck("localidad");

        if ($tipoReporte === "productos_mas_menos") {
            foreach ($periodos as $dias) {
                $desde = now()->subDays($dias);

                $baseQuery = DB::table("productos_vendidos")
                    ->join("ventas", "ventas.id", "=", "productos_vendidos.id_venta")
                    ->join("clientes", "clientes.id", "=", "ventas.id_cliente")
                    ->where("ventas.created_at", ">=", $desde)
                    ->select(
                        "productos_vendidos.codigo_barras",
                        "productos_vendidos.descripcion",
                        DB::raw("SUM(productos_vendidos.cantidad) as total_vendido"),
                        DB::raw("SUM(productos_vendidos.cantidad * productos_vendidos.precio) as monto_total")
                    )
                    ->groupBy("productos_vendidos.codigo_barras", "productos_vendidos.descripcion");

                if (!empty($vendedor)) {
                    $baseQuery->where("ventas.vendedor", $vendedor);
                }

                if (!empty($localidad)) {
                    $baseQuery->where("clientes.localidad", $localidad);
                }

                $masVendidos = (clone $baseQuery)
                    ->orderByDesc("total_vendido")
                    ->limit(15)
                    ->get();

                $menosVendidos = (clone $baseQuery)
                    ->orderBy("total_vendido", "asc")
                    ->limit(15)
                    ->get();

                $reportes[$dias] = [
                    "desde" => $desde,
                    "masVendidos" => $masVendidos,
                    "menosVendidos" => $menosVendidos,
                ];
            }
        } elseif ($tipoReporte === "cantidad_vendida") {
            if (!in_array($agruparPor, ["vendedor", "localidad"], true)) {
                $agruparPor = "vendedor";
            }

            foreach ($periodosCantidad as $dias) {
                $desde = now()->subDays($dias);

                $query = DB::table("productos_vendidos")
                    ->join("ventas", "ventas.id", "=", "productos_vendidos.id_venta")
                    ->join("clientes", "clientes.id", "=", "ventas.id_cliente")
                    ->where("ventas.created_at", ">=", $desde);

                if ($agruparPor === "localidad") {
                    $query->whereNotNull("clientes.localidad")
                        ->where("clientes.localidad", "!=", "")
                        ->select(
                            "clientes.localidad as etiqueta",
                            DB::raw("SUM(productos_vendidos.cantidad * productos_vendidos.precio) as total_monto")
                        )
                        ->groupBy("clientes.localidad");
                } else {
                    $query->whereNotNull("ventas.vendedor")
                        ->where("ventas.vendedor", "!=", "")
                        ->select(
                            "ventas.vendedor as etiqueta",
                            DB::raw("SUM(productos_vendidos.cantidad * productos_vendidos.precio) as total_monto")
                        )
                        ->groupBy("ventas.vendedor");
                }

                $datosOriginales = $query->orderByDesc("total_monto")->get();

                $datos = $datosOriginales->take($topN)->values();
                $restoMonto = $datosOriginales->slice($topN)->sum("total_monto");

                if ($restoMonto > 0) {
                    $datos->push((object) [
                        "etiqueta" => "Otros",
                        "total_monto" => (float) $restoMonto,
                    ]);
                }

                $reportesCantidad[$dias] = [
                    "desde" => $desde,
                    "datos" => $datos,
                    "total_registros" => $datosOriginales->count(),
                ];
            }
        }

        return view("reportes.index", [
            "tiposReporte" => $tiposReporte,
            "tipoReporte" => $tipoReporte,
            "vendedorSeleccionado" => $vendedor,
            "localidadSeleccionada" => $localidad,
            "agruparPor" => $agruparPor,
            "vendedores" => $vendedores,
            "localidades" => $localidades,
            "topN" => $topN,
            "reportes" => $reportes,
            "periodos" => $periodos,
            "reportesCantidad" => $reportesCantidad,
            "periodosCantidad" => $periodosCantidad,
        ]);
    }
}
