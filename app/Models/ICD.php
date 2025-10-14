<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICD extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_master_icd';

    // Primary key
    protected $primaryKey = 'kode_icd';

    // Kalau primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'kode_icd',
        'diagnosis'
    ];

    // Tabel kamu pakai kolom date manual, jadi disable timestamps bawaan Laravel
    public $timestamps = false;
}