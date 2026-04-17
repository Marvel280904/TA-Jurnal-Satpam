<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['username', 'password', 'nama', 'role', 'status', 'group_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password'];

    // Relasi
    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function journals() {
        return $this->hasMany(Journal::class);
    }

    public function updatedJournals() {
        return $this->hasMany(Journal::class, 'updated_by');
    }

    public function handoverJournals() {
        return $this->hasMany(Journal::class, 'handover_by');
    }

    public function approvedJournals() {
        return $this->hasMany(Journal::class, 'approved_by');
    }
}
