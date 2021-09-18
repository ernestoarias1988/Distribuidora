<?php
namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class ProductsImport  implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Producto([
            'codigo_barras'     => $row[0],
            'descripcion'    => $row[1], 
            'precio_compra'    => $row[2], 
            'precio_venta1'    => $row[3], 
            'precio_venta2'    => $row[4], 
            'precio_venta3'    => $row[5], 
            'existencia'    => $row[6], 
        ]);
    }


}

class ProductosExport implements FromCollection,WithStrictNullComparison,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'Nro',
            'Codigo',
            'Descripcion',
            'Precio compra',
            'precio_venta1',
            'precio_venta2',
            'precio_venta3',
            'Cantidad',
            'Fecha Creado',
            'Fecha Modificado'
        ];
    }
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

    public function importar() 
    {
        Excel::import(new ProductsImport,request()->file('file'));        
        return back();
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
            return redirect()->route("productos.index")->with("mensaje", "Producto NO guardado");
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
