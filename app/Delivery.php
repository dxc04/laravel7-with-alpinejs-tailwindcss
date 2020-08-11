<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = ['suject', 'message', 'contact_id', 'campaing_id'];
}
