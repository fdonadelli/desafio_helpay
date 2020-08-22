<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'amount', 'qty_stock'];
    public $timestamps = false;

    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }
}
