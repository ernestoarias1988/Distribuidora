<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ["codigo_barras", "descripcion", "precio_compra","precio_venta1","precio_venta2","precio_venta3", "existencia",
    ];
}
