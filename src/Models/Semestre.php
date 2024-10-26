<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = ['nombre'];

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
}