<?php

namespace Ices\Tool\Service;

use Illuminate\Support\Facades\File;

class ConfigService
{
    public function save($table, $tableConfig)
    {
        if (!file_exists(base_path('ConfigApp.json'))) {
            $configFile = fopen(base_path('ConfigApp.json'), "w");
            fwrite($configFile, "[]");
            fclose($configFile);
        }

        $configJson = file_get_contents(base_path('ConfigApp.json'));
        $config     = json_decode($configJson, true);

        $config[$table] = $tableConfig;
        file_put_contents(base_path('ConfigApp.json'), json_encode($config));
    }
}
