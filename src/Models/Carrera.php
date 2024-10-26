<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable = ['nombre'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
}