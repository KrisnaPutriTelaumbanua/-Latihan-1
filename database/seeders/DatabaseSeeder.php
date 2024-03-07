<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('test@example.com'),
            ]
        );

        DB::table('kategori')->insert([
            'nama_kategori' => 'Nasional'
        ]);
        DB::table('berita')->insert([
            'judul_berita' => 'Lorem Ipsum',
            'isi_berita' => 'Lorem Ipsum',
            'gambar_berita' => 'Loremm.jpg',
            'id_kategori' => 1
        ]);

    }
}
