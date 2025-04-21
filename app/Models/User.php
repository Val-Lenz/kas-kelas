<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'angkatan',
        'jurusan',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function kasKeluar()
    {
        return $this->hasMany(KasKeluar::class);
    }

    // Relasi ke kasMasuk melalui siswa
    public function kasMasuk()
    {
        return $this->hasManyThrough(KasMasuk::class, Siswa::class, 'id_user', 'id_siswa');
    }

}
