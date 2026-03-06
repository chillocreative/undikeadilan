<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@keadilan.my',
            'password' => Hash::make('admin123'),
        ]);

        Election::create([
            'title' => 'Borang Pencalonan Pengerusi Tetap & Timbalan Pengerusi Tetap',
            'slug' => 'pengerusi-tetap',
            'description' => 'Pencalonan dan pemilihan Pengerusi Tetap & Timbalan Pengerusi Tetap',
            'phase' => 'nomination',
            'max_nominations' => 3,
            'max_winners' => 2,
            'winner_labels' => ['Pengerusi Tetap', 'Timbalan Pengerusi Tetap'],
        ]);

        Election::create([
            'title' => 'Pencalonan 5 Nama ke Konvensyen Nasional KEADILAN',
            'slug' => 'konvensyen-nasional',
            'description' => 'Pencalonan dan pemilihan 5 calon ke Konvensyen Nasional KEADILAN',
            'phase' => 'nomination',
            'max_nominations' => 5,
            'max_winners' => 5,
            'winner_labels' => [
                'Calon 1 ke Konvensyen Nasional',
                'Calon 2 ke Konvensyen Nasional',
                'Calon 3 ke Konvensyen Nasional',
                'Calon 4 ke Konvensyen Nasional',
                'Calon 5 ke Konvensyen Nasional',
            ],
        ]);
    }
}
