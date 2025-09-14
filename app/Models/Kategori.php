<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_master_kategori';

    // Primary key
    protected $primaryKey = 'id_kategori';

    // Kalau primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'id_kategori',
        'kategori'
    ];

    // Tabel kamu pakai kolom date manual, jadi disable timestamps bawaan Laravel
    public $timestamps = false;
}