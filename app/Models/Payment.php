<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_master_payment';

    // Primary key
    protected $primaryKey = 'id_pay';

    // Kalau primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'id_pay',
        'payment'
    ];

    // Tabel kamu pakai kolom date manual, jadi disable timestamps bawaan Laravel
    public $timestamps = false;
}