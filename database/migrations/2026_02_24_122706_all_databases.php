<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_grup')->unique();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nama')->unique();
            $table->string('role');
            $table->string('status')->default('Active');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi')->unique();
            $table->text('alamat_lokasi');
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift')->unique();
            $table->time('mulai_shift');
            $table->time('selesai_shift');
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('group_id')->constrained('groups');
            $table->foreignId('lokasi_id')->constrained('locations');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->foreignId('next_shift')->constrained('groups');
            
            // Data Jurnal
            $table->text('laporan_kegiatan');
            $table->text('kejadian_temuan');
            $table->string('lembur');
            $table->text('proyek_vendor');
            $table->text('barang_inven');
            $table->text('info_tambahan')->nullable();
            
            // Status & Approval
            $table->string('status')->default('Pending');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('handover_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('catatan')->nullable();
            
            $table->timestamps();

            // UNIQUE CONSTRAINT untuk mencegah race condition
            // Memastikan 1 grup hanya bisa punya 1 jurnal per tanggal,lokasi, dan shift
            $table->unique(['group_id', 'tanggal', 'lokasi_id', 'shift_id'], 'unique_journal_composite');
        });

        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->onDelete('cascade');
            $table->string('file_path'); // Lokasi penyimpanan file (misal: storage/uploads/...)
            $table->string('file_name'); // Nama asli file
            $table->timestamps();
        });

        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('aksi'); // Misal: Create User, Login, dll
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('users');
        Schema::dropIfExists('groups');
    }
};
