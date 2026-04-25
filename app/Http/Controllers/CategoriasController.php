<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Producto;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $usoPorCategoria = Producto::selectRaw('categoria, count(*) as total')
            ->groupBy('categoria')
            ->pluck('total', 'categoria');

        return view('categorias.index', [
            'categorias' => $categorias,
            'usoPorCategoria' => $usoPorCategoria,
        ]);
    }

    public function store(Request $request)
    {
        $nombre = trim((string) $request->input('nombre'));
        if ($nombre === '') {
            return redirect()
                ->route('categorias.index')
                ->with(['mensaje' => 'La categoria es obligatoria', 'tipo' => 'danger']);
        }

        $existe = Categoria::whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])->first();
        if ($existe) {
            return redirect()
                ->route('categorias.index')
                ->with(['mensaje' => 'La categoria ya existe', 'tipo' => 'danger']);
        }

        Categoria::create(['nombre' => $nombre]);

        return redirect()
            ->route('categorias.index')
            ->with('mensaje', 'Categoria creada');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $nombreNuevo = trim((string) $request->input('nombre'));
        if ($nombreNuevo === '') {
            return redirect()
                ->route('categorias.index')
                ->with(['mensaje' => 'La categoria es obligatoria', 'tipo' => 'danger']);
        }

        $duplicada = Categoria::where('id', '!=', $categoria->id)
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombreNuevo)])
            ->first();
        if ($duplicada) {
            return redirect()
                ->route('categorias.index')
                ->with(['mensaje' => 'Ya existe una categoria con ese nombre', 'tipo' => 'danger']);
        }

        $nombreAnterior = $categoria->nombre;
        $categoria->nombre = $nombreNuevo;
        $categoria->saveOrFail();

        Producto::where('categoria', $nombreAnterior)->update(['categoria' => $nombreNuevo]);

        return redirect()
            ->route('categorias.index')
            ->with('mensaje', 'Categoria actualizada');
    }

    public function destroy(Categoria $categoria)
    {
        if (mb_strtolower($categoria->nombre) === 'general') {
            return redirect()
                ->route('categorias.index')
                ->with(['mensaje' => 'La categoria General no se puede eliminar', 'tipo' => 'danger']);
        }

        Categoria::firstOrCreate(['nombre' => 'General']);
        Producto::where('categoria', $categoria->nombre)->update(['categoria' => 'General']);
        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('mensaje', 'Categoria eliminada y productos reasignados a General');
    }
}