<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartNumber extends Model
{
    protected $fillable = [
    	'item_code', 
    	'inc', 
    	'item_name', 
    	'short_desc', 
    	'man_code', 
    	'man_name', 
    	'part_number', 
    	'po_text',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function partnumbers()
    {
        return $this->hasMany('App\PartNumber', 'item_code', 'item_code');
    }
}
