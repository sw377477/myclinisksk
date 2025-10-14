<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryKunjungan extends Model
{
    // Nama tabel
    protected $table = 'rme_entry_kunjungan';

    // Primary key
    protected $primaryKey = 'no_kunjungan';

    // Karena primary key bukan auto increment
    public $incrementing = false;

    // Primary key bertipe string
    protected $keyType = 'string';

    // Tidak menggunakan created_at / updated_at otomatis
    public $timestamps = false;

    // Kolom yang dapat diisi mass-assignment
    protected $fillable = [
        'tgl_kunjungan',
        'jam_kunjungan',
        'no_kunjungan',
        'status_lk',
        'jenis_kunjungan',
        'id_member',
        'id_poli',
        'id_pay',   // jenis pembayaran
        'idpay'     // identitas lokasi (baru ditambahkan)
    ];

    /* ==============================
       RELASI ANTAR MODEL
       ============================== */

    // Relasi ke tabel member (pasien)
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    // Relasi ke tabel poli
    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli', 'id_poli');
    }

    // Relasi ke tabel jenis pembayaran
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'id_pay', 'id_pay');
    }

    // Relasi ke tabel lokasi (misal rme_master_lokasi)
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'idpay', 'idpay');
    }
}
