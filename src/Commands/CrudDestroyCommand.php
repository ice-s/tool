<?php

namespace Ices\Tool\Commands;

use Illuminate\Console\Command;

class CrudDestroyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:destroy';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->deleteDir(app_path("/Entities"));
        $this->deleteDir(app_path("/Services"));
        $this->deleteDir(app_path("/Resources"));
        $this->deleteDir(app_path("/Repositories"));
        $this->deleteDir(app_path("/Http/Requests"));

        $this->deleteDir(app_path("/Http/Controllers/Api"));
        $this->deleteDir(app_path("/Http/Controllers/Web"));
        $this->deleteDir(base_path('routes/Web'));
        $this->deleteDir(base_path('routes/Api'));
    }

    public function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
