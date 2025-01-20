<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FileController; // <-- adjust the namespace/class as needed

class RegenerateMetaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * e.g. "php artisan meta:regenerate-data"
     */
    protected $signature = 'meta:regenerate-data';

    /**
     * The console command description.
     */
    protected $description = 'Regenerate metadata (people, keywords) for all Files';

    /**
     * Execute the console command.
     */
    public function handle(FileController $controller)
    {
        $this->info('Starting meta data regeneration...');

        $controller->regenerateMetaData();

        $this->info('Meta data regeneration completed.');
    }
}
