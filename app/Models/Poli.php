<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'rme_master_poli';

    // ✅ Gunakan id_serial sebagai primary key
    protected $primaryKey = 'id_serial';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_poli',
        'poli',
        'nama_medis',
        'idpay',
    ];

    public $timestamps = false;
}
