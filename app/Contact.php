<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $casts = [
        'info' => 'array',
    ];

    protected $fillable = ['email', 'info'];
}
