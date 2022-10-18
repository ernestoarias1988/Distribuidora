<?php

use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route("home");
});
Route::get("/acerca-de", function () {
    return view("");
})->name("acerca_de.index");
Route::get("/soporte", function () {
    return redirect("");
})->name("soporte.index");

Auth::routes([
    "reset" => false, // no pueden olvidar contraseña
]);


// Permitir logout con petición get
Route::get("/logout", function () {
    Auth::logout();
    return redirect()->route("home");
})->name("logout");

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/exportarp', 'ProductosController@export');
Route::get('/exportarv', 'VentasController@export');


Route::middleware("auth")
    ->group(function () {
        Route::resource("clientes", "ClientesController");
        Route::resource("usuarios", "UserController")->parameters(["usuarios" => "user"]);
        Route::resource("productos", "ProductosController");
        Route::get("/ventas/ticket", "VentasController@ticket")->name("ventas.ticket");
        Route::resource("ventas", "VentasController");
        Route::get("/vender", "VenderController@index")->name("vender.index");
        Route::post("/productoDeVenta", "VenderController@agregarProductoVenta")->name("agregarProductoVenta");
        Route::delete("/productoDeVenta", "VenderController@quitarProductoDeVenta")->name("quitarProductoDeVenta");
        Route::post("/terminarOCancelarVenta", "VenderController@terminarOCancelarVenta")->name("terminarOCancelarVenta");

        Route::get('user-list-pdf', 'VentasController@exportPdf')->name('users.pdf');
        Route::get('indexNoShowEntregados', 'VentasController@indexNoShowEntregados')->name('ventas.indexNoShowEntregados');
        Route::get('indexSiShowEntregados', 'VentasController@indexSiShowEntregados')->name('ventas.indexSiShowEntregados');
        Route::get('ventas-list-pdf', 'VentasController@exportVentasPdf')->name('ventas.pdf');
        Route::get('cancelarpago', 'VentasController@cancelarPago')->name('cancelPago');
        Route::get('cancelarentrega', 'VentasController@cancelarEntrega')->name('cancelEntrega');
        Route::post('cargarpago', 'VentasController@cargarPago')->name('cargaPago');
        Route::get('cargarentrega', 'VentasController@cargarEntrega')->name('cargaEntrega');
        Route::post('agregarproducto', 'VenderController@agregarProductoACarrito')->name('agregaProducto');
        Route::post('guardarCliente', 'VenderController@guardarCliente')->name('guardarCliente');
        Route::post('guardarLocalidad', 'VentasController@guardarLocalidad')->name('guardarLocalidad');


        Route::post('editarcantidad', 'VenderController@editarCantidad')->name('editaCantidad');

        Route::post('import', 'ProductosController@importar')->name('import');
        Route::get('/delete', 'ProductosController@deleteAll')->name('delete');

        Route::post('/autocomplete/fetch', 'VenderController@fetch')->name('autocomplete.fetch');
        Route::post('/autocomplete/fetchcliente', 'VenderController@fetchcliente')->name('autocomplete.fetchcliente');
        Route::post('/autocomplete/fetchvendedor', 'ClientesController@fetchvendedor')->name('autocomplete.fetchvendedor');
        Route::post('/autocomplete/fetchlocalidad', 'VentasController@fetchlocalidad')->name('autocomplete.fetchlocalidad');
        Route::post('/autocomplete/fetchcantidad', 'VenderController@fetchcantidad')->name('autocomplete.fetchcantidad');
        Route::get("/totales", "TotalesController@index")->name("totales.index");

        Route::post("/ventas", "VentasController@destroyProducto")->name("destroyProducto");
        Route::post('cargarCantidad', 'VentasController@cargarCantidad')->name('cargaCantidad');
        Route::post('cargarCantidadShow', 'VentasController@cargarCantidadShow')->name('cargaCantidadShow');
    });
