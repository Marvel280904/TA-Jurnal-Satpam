<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['nama_grup'];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function journals() {
        return $this->hasMany(Journal::class);
    }
}