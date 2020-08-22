<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['product_id', 'purchase_date', 'total', 'card'];
    public $timestamps = false;
}
