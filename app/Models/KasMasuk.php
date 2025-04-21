<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMasuk extends Model
{
    use HasFactory;
    protected $table = 'kas_masuk';
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
