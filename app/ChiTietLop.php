<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChiTietlop extends Model
{
    protected $table = 'CHITIETLOPHOC';
    protected $fillable = [
        'MaLop', 'MaHocVien', 'Diem', 'DanhGia'
    ];

    public $timestamps = false;
}
