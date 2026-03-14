<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeVueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:vue {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Vue page';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = resource_path("js/Pages/{$name}.vue");
        $directory = dirname($path);

        // Create directories if they don't exist
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->error("File already exists!");
            return;
        }

        $template = "<script setup>\n\n</script>\n\n<template>\n    <div>\n\n    </div>\n</template>";

        File::put($path, $template);
        $this->info("Vue page created at: {$path}");
    }
}
