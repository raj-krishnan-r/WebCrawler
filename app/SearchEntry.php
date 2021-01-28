<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchEntry extends Model
{
    protected $fillable = ['rootHash','title','content','content_hash','root'];
    protected $table='search_index';
    protected $primaryKey='id';
}
