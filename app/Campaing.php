<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaing extends Model
{
    protected $casts = [
        'requiredVars' => 'array',
        'mappedVars' => 'array',
        'templates' => 'array'
    ];

    protected $fillable = ['name', 'templates', 'requiredVars', 'mappedVars'];
}
