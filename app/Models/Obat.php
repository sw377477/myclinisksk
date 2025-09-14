<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_master_obat';

    // Primary key
    protected $primaryKey = 'id_obat';

    // Kalau primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'id_obat',
        'kode_obat',
        'nama_obat',
        'kategori',
        'satuan',
        'golongan',
        'stok_minimal',
        'is_aktif',
        'tgl_input',
        'tgl_update',
        'user_input',
        'user_update'
    ];

    // Tabel kamu pakai kolom date manual, jadi disable timestamps bawaan Laravel
    public $timestamps = false;
}
