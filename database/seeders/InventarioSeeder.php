<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Producto;
use App\Models\Entrada;

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
            Tipo::firstOrCreate(['id' => $tipo['id'], 'tipo' => $tipo['tipo']]);
        }

        //CREACION DE MARCAS
        $marcas = [
            ['id' => 1, 'marca' => 'Suzuki'],
            ['id' => 2, 'marca' => 'Toyota'],
            ['id' => 3, 'marca' => 'Jeep'],
            ['id' => 4, 'marca' => 'Ford'],
            ['id' => 5, 'marca' => 'Land Rover'],
            ['id' => 6, 'marca' => 'Mercedes-Benz'],
            ['id' => 7, 'marca' => 'Nissan'],
            ['id' => 8, 'marca' => 'Mitsubishi'],
            ['id' => 9, 'marca' => 'Chevrolet'],
            ['id' => 10, 'marca' => 'Volkswagen'],
        ];
        foreach ($marcas as $marca) {
            Marca::firstOrCreate(['id' => $marca['id'], 'marca' => $marca['marca']]);
        }
        
        //CREACION DE MODELOS
        $modelos = [
            ['id' => 1, 'marca_id' => 1, 'modelo' => 'Suzuki Jimny AllGrip'],
            ['id' => 2, 'marca_id' => 2, 'modelo' => 'Toyota Land Cruiser'],
            ['id' => 3, 'marca_id' => 3, 'modelo' => 'Jeep Wrangler Rubicon'],
            ['id' => 4, 'marca_id' => 4, 'modelo' => 'Ford Bronco Badlands'],
            ['id' => 5, 'marca_id' => 5, 'modelo' => 'Land Rover Defender 110'],
            ['id' => 6, 'marca_id' => 6, 'modelo' => 'Mercedes-Benz Clase G 500'],
            ['id' => 7, 'marca_id' => 7, 'modelo' => 'Nissan Patrol Y62'],
            ['id' => 8, 'marca_id' => 8, 'modelo' => 'Mitsubishi Montero Sport'],
            ['id' => 9, 'marca_id' => 9, 'modelo' => 'Chevrolet Tahoe Z71'],
            ['id' => 10, 'marca_id' => 10, 'modelo' => 'Volkswagen Amarok'],
        ];
        foreach ($modelos as $modelo) {
            Modelo::firstOrCreate([
                'id' => $modelo['id'],
                'marca_id' => $modelo['marca_id'],
                'modelo' => $modelo['modelo']
            ]);
        }

        //CREACION DE PRODUCTOS
        $productos = [
            ['id' => 1, 'tipo_id' => 1, 'modelo_id' => 1, 'producto' => 'Kits de levantamiento (lift kits) específicos por modelo', 'accesorio' => false, 'precio' => 1499.99],
            ['id' => 2, 'tipo_id' => 1, 'modelo_id' => 2, 'producto' => 'Amortiguadores monotubo de alto desempeño', 'accesorio' => false, 'precio' => 1280.25],
            ['id' => 3, 'tipo_id' => 1, 'modelo_id' => 3, 'producto' => 'Resortes helicoidales reforzados para carga extra', 'accesorio' => false, 'precio' => 1120.85],
            ['id' => 4, 'tipo_id' => 1, 'modelo_id' => 4, 'producto' => 'Barras estabilizadoras heavy duty (delantera/trasera)', 'accesorio' => false, 'precio' => 1350.70],
            ['id' => 5, 'tipo_id' => 1, 'modelo_id' => 5, 'producto' => 'Bujes de poliuretano para brazos de suspensión', 'accesorio' => false, 'precio' => 870.45],
            ['id' => 6, 'tipo_id' => 1, 'modelo_id' => 6, 'producto' => 'Extensores o relocadores de amortiguadores', 'accesorio' => false, 'precio' => 650.99],
            ['id' => 7, 'tipo_id' => 1, 'modelo_id' => 7, 'producto' => 'Topes de suspensión ajustables para recorridos largos', 'accesorio' => false, 'precio' => 760.11],
            ['id' => 8, 'tipo_id' => 1, 'modelo_id' => 8, 'producto' => 'Kits de drop brackets para mantener geometría tras el lift', 'accesorio' => false, 'precio' => 920.77],
            ['id' => 9, 'tipo_id' => 1, 'modelo_id' => 9, 'producto' => 'Brazos de control (control arms) tubulares ajustables', 'accesorio' => false, 'precio' => 1500.00],
            ['id' => 10, 'tipo_id' => 1, 'modelo_id' => 10, 'producto' => 'Espaciadores de ejes o muelles para nivelación del tren delantero', 'accesorio' => false, 'precio' => 305.33],
            ['id' => 11, 'tipo_id' => 2, 'modelo_id' => 1, 'producto' => 'Barra de luz adaptativa Baja Designs OnX6', 'accesorio' => true, 'precio' => 900.00],
            ['id' => 12, 'tipo_id' => 2, 'modelo_id' => 2, 'producto' => 'Sistema KC HiLiTES Gravity LED Pro6 Crossbar (versión adaptativa)', 'accesorio' => true, 'precio' => 1250.00],
            ['id' => 13, 'tipo_id' => 2, 'modelo_id' => 3, 'producto' => 'Barra luminosa Rigid Industries Adapt XP', 'accesorio' => true, 'precio' => 1100.00],
            ['id' => 14, 'tipo_id' => 2, 'modelo_id' => 4, 'producto' => 'Faros inteligentes ARB Intensity IQ', 'accesorio' => true, 'precio' => 1300.00],
            ['id' => 15, 'tipo_id' => 2, 'modelo_id' => 5, 'producto' => 'Kit de faros Hella Rallye 4000 Xenón con módulo adaptativo', 'accesorio' => true, 'precio' => 1400.00],
            ['id' => 16, 'tipo_id' => 2, 'modelo_id' => 6, 'producto' => 'Barra LED Lazer Lamps Triple-R 1250 con Smartview', 'accesorio' => true, 'precio' => 1200.00],
            ['id' => 17, 'tipo_id' => 2, 'modelo_id' => 7, 'producto' => 'Faros Vision X ADV Light Cannon Multi-LED', 'accesorio' => true, 'precio' => 1350.00],
            ['id' => 18, 'tipo_id' => 2, 'modelo_id' => 8, 'producto' => 'Proyectores PIAA de la serie LP con haz adaptativo', 'accesorio' => true, 'precio' => 980.00],
            ['id' => 19, 'tipo_id' => 2, 'modelo_id' => 9, 'producto' => 'Faros JW Speaker Modelo 8700 Evolution 2 Dual Burn', 'accesorio' => true, 'precio' => 1600.00],
            ['id' => 20, 'tipo_id' => 2, 'modelo_id' => 1, 'producto' => 'Focos Osram LEDriving con control dinámico', 'accesorio' => true, 'precio' => 950.00],
            ['id' => 21, 'tipo_id' => 3, 'modelo_id' => 1, 'producto' => 'Toldo lateral impermeable para techo de 4x4', 'accesorio' => true, 'precio' => 800.00],
            ['id' => 22, 'tipo_id' => 3, 'modelo_id' => 2, 'producto' => 'Caja de techo aerodinámica con candado', 'accesorio' => true, 'precio' => 620.00],
            ['id' => 23, 'tipo_id' => 3, 'modelo_id' => 3, 'producto' => 'Protector de matrícula magnético para off-road', 'accesorio' => true, 'precio' => 150.00],
            ['id' => 24, 'tipo_id' => 3, 'modelo_id' => 4, 'producto' => 'Kit de recuperación con eslinga y guantes', 'accesorio' => true, 'precio' => 210.00],
            ['id' => 25, 'tipo_id' => 3, 'modelo_id' => 5, 'producto' => 'Cubierta resistente para rueda de repuesto', 'accesorio' => true, 'precio' => 95.00],
            ['id' => 26, 'tipo_id' => 3, 'modelo_id' => 6, 'producto' => 'Mesa plegable con fijación para parachoques', 'accesorio' => true, 'precio' => 175.00],
            ['id' => 27, 'tipo_id' => 3, 'modelo_id' => 7, 'producto' => 'Compresor de aire portátil con manómetro', 'accesorio' => true, 'precio' => 320.00],
            ['id' => 28, 'tipo_id' => 3, 'modelo_id' => 8, 'producto' => 'Fundas impermeables para asientos de vehículo', 'accesorio' => true, 'precio' => 110.00],
            ['id' => 29, 'tipo_id' => 3, 'modelo_id' => 9, 'producto' => 'Estribos laterales para carga de equipo', 'accesorio' => true, 'precio' => 420.00],
            ['id' => 30, 'tipo_id' => 3, 'modelo_id' => 1, 'producto' => 'Panel solar flexible con batería integrada', 'accesorio' => true, 'precio' => 690.00],
        ];
        foreach ($productos as $producto) {
            Producto::firstOrCreate([
                'id' => $producto['id'],
                'tipo_id' => $producto['tipo_id'],
                'modelo_id' => $producto['modelo_id'],
                'producto' => $producto['producto'],
                'accesorio' => $producto['accesorio'],
                'precio' => $producto['precio'],
            ]);
        }

        //CREACION DE STOCKS
        $stocks = [
            ['id' => 1, 'stock' => 'Proveedor'],
            ['id' => 2, 'stock' => 'Bodega'],
            ['id' => 3, 'stock' => 'Estante de reservados'],
            ['id' => 4, 'stock' => 'Reservados en tránsito'],
        ];
        foreach ($stocks as $stock) {
            Stock::firstOrCreate([
                'id'    => $stock['id'],
                'stock' => $stock['stock'],
            ]);
        }

    }
}
