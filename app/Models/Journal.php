<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'tanggal', 'user_id', 'group_id', 'lokasi_id', 'shift_id', 'next_shift',
        'laporan_kegiatan', 'kejadian_temuan', 'lembur', 'proyek_vendor', 
        'barang_inven', 'info_tambahan', 'status', 'updated_by', 'handover_by', 
        'approved_by', 'catatan'
    ];

    // Relasi
    public function location() {
        return $this->belongsTo(Location::class, 'lokasi_id');
    }

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function nextShift() {
        return $this->belongsTo(Group::class, 'next_shift');
    }

    public function user() { return $this->belongsTo(User::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function uploads() { return $this->hasMany(Upload::class); }

    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
    public function handover() { return $this->belongsTo(User::class, 'handover_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    // Methods
    public static function viewJournal() {
        return self::all();
    }

    public function handoverApproval($userId, $status) {
        $this->handover_by = $userId;
        $this->status = $status;
        return $this->save();
    }

    public function finalApproval($userId, $status) {
        $this->approved_by = $userId;
        $this->status = $status;
        return $this->save();
    }
}