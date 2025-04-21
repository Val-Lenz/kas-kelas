<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKeluar extends Model
{
    use HasFactory;
    protected $table = 'kas_keluar';

    protected $fillable = [
        'nominal',
        'catatan',
        '_token', // Include _token in fillable to allow mass assignment
        'id_user',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
