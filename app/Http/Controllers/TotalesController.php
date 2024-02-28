<?php

namespace App\Http\Controllers;
use App\Producto;
use App\User;
use App\ProductoVendido;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TotalesController extends Controller
{
    public function index(Request $request)
    {        

        $vendedor=request("vendedor");
        $localidad = request("localidad");
        $ventasConTotales = Venta::join("productos_vendidos", "productos_vendidos.id_venta", "=", "ventas.id")
        ->select("ventas.*", DB::raw("sum(productos_vendidos.cantidad * productos_vendidos.precio) as total"))
        ->groupBy("ventas.id", "ventas.pagado", "ventas.entregado", "ventas.created_at", "ventas.updated_at","ventas.vendedor","ventas.id_cliente","ventas.idApp")
        ->get();
    return view("totales.totales", ["ventas" => $ventasConTotales,],["vendedor" =>$vendedor,"localidad"=>$localidad]);
    }
}
