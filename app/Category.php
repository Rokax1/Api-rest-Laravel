<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table='categories';

    //relacion de uno amuchos 
    public function posts (){
        return $this->hasMany('App\Post');

    }
}
