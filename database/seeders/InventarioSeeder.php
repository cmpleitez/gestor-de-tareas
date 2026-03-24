<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        //CREACION DE TIPOS DE PRODUCTOS
        $tipos = [
            ['id' => 1, 'tipo' => 'Accesorios de suspensión'],
            ['id' => 2, 'tipo' => 'Luces adaptativas'],
            ['id' => 3, 'tipo' => 'Camping para vehículos'],
        ];
        foreach ($tipos as $tipo) {
            DB::table('tipos')->updateOrInsert(
                ['id' => $tipo['id']],
                [
                    'tipo' => $tipo['tipo'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

        //CREACION DE STOCKS
        $stocks = [
            ['id' => 1, 'stock' => 'Proveedor'],
            ['id' => 2, 'stock' => 'Bodega'],
            ['id' => 3, 'stock' => 'Estante de reservados'],
            ['id' => 4, 'stock' => 'Reservados en tránsito'],
        ];
        foreach ($stocks as $stock) {
            DB::table('stocks')->updateOrInsert(
                ['id'    => $stock['id']],
                [
                    'stock' => $stock['stock'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
