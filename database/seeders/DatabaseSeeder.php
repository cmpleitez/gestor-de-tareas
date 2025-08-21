<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders existentes
            // UserSeeder::class,
            // ... otros seeders existentes
            
            // ========================================
            // SEEDERS DE SEGURIDAD - MONITOREO AVANZADO
            // ========================================
            UserSeeder::class,
        ]);
    }
}

