<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Danhsachlop extends Model
{
    protected $table = 'DANHSACHHOCSINHTRONGLOP';
    protected $fillable = [
        'User ID', 'Mã Lớp', 'Mã Lớp Chuyển', 'Thời Gian Chuyển'
    ];

    public $timestamps = false;
}
