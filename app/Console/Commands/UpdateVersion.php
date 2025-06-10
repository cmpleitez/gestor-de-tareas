<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateVersion extends Command
{
    protected $signature = 'version:update {type : The type of update (major|minor|patch)}';
    protected $description = 'Actualiza la versión de la aplicación';

    public function handle()
    {
        $configPath = config_path('app.php');
        $config = file_get_contents($configPath);
        
        // Obtener versión actual
        preg_match("/'version' => '(.*?)'/", $config, $matches);
        $currentVersion = $matches[1];
        
        // Separar versión en partes
        list($major, $minor, $patch) = explode('.', $currentVersion);
        
        // Actualizar según el tipo
        switch ($this->argument('type')) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
        }
        
        // Nueva versión
        $newVersion = "{$major}.{$minor}.{$patch}";
        
        // Reemplazar en el archivo
        $newConfig = preg_replace(
            "/'version' => '(.*?)'/",
            "'version' => '{$newVersion}'",
            $config
        );
        
        file_put_contents($configPath, $newConfig);
        
        $this->info("Versión actualizada a: {$newVersion}");
    }
} 