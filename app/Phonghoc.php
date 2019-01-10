<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phonghoc extends Model
{
    protected $table = 'PHONGHOC';
    protected $fillable = [
        'Mã Phòng Học', 'Sức Chứa', 'Ghi Chú', 'branch'
    ];

    public $timestamps = false;

    protected $hidden = ['ID'];
}
