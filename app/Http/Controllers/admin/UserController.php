<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'bendahara')->get();
        $users = $users->sortBy('angkatan');
        return view('admin.bendahara', compact('users'));
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:users,nama',
            'angkatan' => 'required|integer',
            'jurusan' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'jurusan' => $request->jurusan,
            'password' => Hash::make($request->password),
            'role' => 'bendahara',
        ]);

        return redirect()->route('admin.bendahara')
            ->with('success', 'Bendahara berhasil ditambahkan!');
    }

    public function update(Request $request, User $bendahara)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:users,nama,' . $bendahara->id,
            'angkatan' => 'required|integer',
            'jurusan' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $bendahara->update([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'jurusan' => $request->jurusan,
            'password' => $request->filled('password')
                ? Hash::make($request->password)
                : $bendahara->password,
        ]);

        return redirect()->route('admin.bendahara')
            ->with('success', 'Data bendahara berhasil diperbarui!');
    }

    // Parameter diganti menjadi $bendahara, untuk konsistensi binding
    public function destroy(User $bendahara)
    {
        $bendahara->delete();
        return redirect()->route('admin.bendahara')
            ->with('success', 'Bendahara berhasil dihapus!');
    }
}