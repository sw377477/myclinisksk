<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_master_satuan';

    // Primary key
    protected $primaryKey = 'id_satuan';

    // Kalau primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'id_satuan',
        'satuan'
    ];

    // Tabel kamu pakai kolom date manual, jadi disable timestamps bawaan Laravel
    public $timestamps = false;
}