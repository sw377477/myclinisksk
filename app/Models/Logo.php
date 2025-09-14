<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    // Nama tabel di PostgreSQL
    protected $table = 'rme_logo';

    // Primary key
    protected $primaryKey = 'iddata';

    // Primary key bukan integer auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'iddata',
        'logo'
    ];

    // Disable timestamps bawaan Laravel
    public $timestamps = false;
}
