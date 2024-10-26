<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = [
        'alumno_id',
        'curso_id',
        'seccion_id',
        'semestre',
        'nota'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
}