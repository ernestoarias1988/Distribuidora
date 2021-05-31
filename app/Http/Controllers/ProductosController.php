<?php
namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductosExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Producto::all();
    }
}
class ProductosController extends Controller
{
    public function export() 
    {
        return Excel::download(new ProductosExport, 'Productos.xlsx');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("productos.productos_index", ["productos" => Producto::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("productos.productos_create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = new Producto($request->input());

        $probando = Producto::where("codigo_barras", "=", $producto->codigo_barras)->first();
        if ($probando) {
            return redirect()
                ->route("productos.index")
                ->with([
                    "mensaje" => "Codigo ya existente. Producto no creado",
                    "tipo" => "danger"
                ]);
        }
        $probando = Producto::where("descripcion", "=", $producto->descripcion)->first();
        if($probando) {
            return redirect()
                ->route("productos.index")
                ->with([
                    "mensaje" => "Producto ya existente",
                    "tipo" => "danger"
                ]);

        }





        if($producto == Producto::find($producto->id))
        {
            return redirect()->route("productos.index")->with("mensaje", "Producto NOOO guardado");
        }
        $producto = new Producto($request->input());
        $producto->saveOrFail();
        return redirect()->route("productos.index")->with("mensaje", "Producto guardado");
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto)
    {
        return view("productos.productos_edit", ["producto" => $producto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $producto->fill($request->input());
        $producto->saveOrFail();
        return redirect()->route("productos.index")->with("mensaje", "Producto actualizado");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route("productos.index")->with("mensaje", "Producto eliminado");
    }
}
