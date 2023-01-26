<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    public function bouteilles()
    {
        return $this->hasMany(Bouteille::class);
    }

    public function bouteillesSaq()
    {
        return $this->hasMany(BouteilleSaq::class);
    }
}
