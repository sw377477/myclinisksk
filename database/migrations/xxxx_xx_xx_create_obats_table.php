
public function up()
{
    Schema::create('obats', function (Blueprint $table) {
        $table->id();
        $table->string('kode_obat')->unique();
        $table->string('nama_obat');
        $table->integer('stok');
        $table->string('satuan');
        $table->timestamps();
    });
}
