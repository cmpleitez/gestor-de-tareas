<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportCatalogService;
use Illuminate\Support\Facades\File;

class ImportCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:importar {file=docs/formato-importacion-full.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa el catálogo de productos y kits desde un archivo Excel';

    /**
     * Execute the console command.
     */
    public function handle(ImportCatalogService $importService)
    {
        $file = $this->argument('file');

        if (!File::exists(base_path($file))) {
            $this->error("El archivo no existe: " . $file);
            return 1;
        }

        $this->info("Iniciando importación desde: " . $file);

        try {
            $result = $importService->import(base_path($file));

            $this->info("Importación completada.");
            $this->line("- Filas procesadas con éxito: " . $result['total']);

            if (count($result['errors']) > 0) {
                $this->warn("- Se encontraron " . count($result['errors']) . " errores.");
                foreach ($result['errors'] as $error) {
                    $this->error("  " . $error);
                }
            } else {
                $this->info("- Sin errores detectados.");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error crítico durante la importación: " . $e->getMessage());
            return 1;
        }
    }
}
