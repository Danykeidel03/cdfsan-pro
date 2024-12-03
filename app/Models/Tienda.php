<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;
    protected $table = "tblTiendas";
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'razonSocial',
        'direccion',
        'codigoPostal',
        'provincia',
        'pais',
        'nif',
    ];
    protected $hidden = [];
    }
