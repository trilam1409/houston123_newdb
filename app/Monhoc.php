<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monhoc extends Model
{
    protected $table = 'DANHSACHMONHOC';

    protected $fillable = [
        'mamon', 'name', 'managerAllow',
    ];

    public $timestamps = false;
}
