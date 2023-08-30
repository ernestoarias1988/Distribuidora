<?php

namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;

use Artisan;
use Illuminate\Support\Composer;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class ProductsImport  implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        /* echo '<pre>';
        print_r($row);
        echo '</pre>';*/
        if ($row['articulo'] != null) {
            /*   if ($row['cantidad'] == null) {
                $row['cantidad'] = 1;
            }*/
            if ($row['precio1'] == null) {
                $row['precio1'] = 0;
            }        
            if ($row['precio2'] == null) {
                $row['precio2'] = $row['precio1'];
            }           
            if ($row['precio3'] == null) {
                $row['precio3'] = $row['precio1'];
            }
            if ($row['existencia'] == null) {
                $row['existencia'] = 1;
            }
            /* if ($row['promo'] == null) {
                $row['promo'] = $row['precio_real'];
            }*/
            if (Producto::where("descripcion", "=", $row['articulo'])->first() == null) {
                return new Producto([
                    'codigo_barras'     => $row['codigo'],
                    'descripcion'    => $row['articulo'],
                    'precio_compra'    => $row['precio1'],
                    'precio_venta1'    => $row['precio1'],
                    'precio_venta2'    => $row['precio2'],
                    'precio_venta3'    => $row['precio3'],
                    'existencia'    => $row['existencia'],
                ]);
            } else {
                $productoActualizando = Producto::where("descripcion", "=", $row['articulo'])->first();
                $productoActualizando->codigo_barras = $row['codigo'];
                $productoActualizando->precio_compra = $row['precio1'];
                $productoActualizando->precio_venta1 = $row['precio1'];
                $productoActualizando->precio_venta2 = $row['precio2'];
                $productoActualizando->precio_venta3 = $row['precio3'];
                $productoActualizando->existencia = $row['existencia'];
                $productoActualizando->saveOrFail();
            }
        } else {
            //Agregar mensaje de error
        }
    }
}

class ProductosExport implements FromCollection, WithStrictNullComparison, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'codigo',
            'articulo',
            'Precio1',
            'Precio2',
            'Precio3',
            'existencia'
        ];
    }
    /*public function collection()
    {
        return Producto::all();
        
    }*/

    public function collection()
    {
        return Producto::select('codigo_barras','descripcion', 'precio_venta1','precio_venta2','precio_venta3', 'existencia')->get();
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
        Excel::import(new ProductsImport, request()->file('file'));
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
        if ($probando) {
            return redirect()
                ->route("productos.index")
                ->with([
                    "mensaje" => "Producto ya existente",
                    "tipo" => "danger"
                ]);
        }

        if ($producto == Producto::find($producto->id)) {
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
        return view("productos.productos_edit", [
            "producto" => $producto,
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
        


        $probando = Producto::where("codigo_barras", "=", $producto->codigo_barras)->first();
        
        if ($probando) {
            if($probando->descripcion!=$producto->descripcion){
            return redirect()
                ->route("productos.index")
                ->with([
                    "mensaje" => "Codigo ya existente. Producto no creado",
                    "tipo" => "danger"
                ]);
            }}
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

    public function deleteAll()
    {
        while (true) {
            $producto = Producto::where("descripcion", "LIKE", '%a%')->first();
            if ($producto == null) {
                break;
            }
            $producto->delete();
        }
        while (true) {
            $producto = Producto::where("descripcion", "LIKE", '%e%')->first();
            if ($producto == null) {
                break;
            }
            $producto->delete();
        }
        while (true) {
            $producto = Producto::where("descripcion", "LIKE", '%i%')->first();
            if ($producto == null) {
                break;
            }
            $producto->delete();
        }
        while (true) {
            $producto = Producto::where("descripcion", "LIKE", '%o%')->first();
            if ($producto == null) {
                break;
            }
            $producto->delete();
        }
        while (true) {
            $producto = Producto::where("descripcion", "LIKE", '%u%')->first();
            if ($producto == null) {
                break;
            }
            $producto->delete();
        }
        return redirect()->route("productos.index")->with("mensaje", "Productos eliminados");
    }
}
