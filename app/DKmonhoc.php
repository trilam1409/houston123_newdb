<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DKmonhoc extends Model
{
    protected $table = 'DANGKIMONHOC';
    protected $fillable = [
        'User ID', 'monhoc', 'ngaydangky'
    ];

    public $timestamps = false;
   
}
