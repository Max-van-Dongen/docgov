<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FileController;

class RegeneratePdfData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * e.g. "php artisan pdf:regenerate-data"
     */
    protected $signature = 'pdf:regenerate-data';

    /**
     * The console command description.
     */
    protected $description = 'Regenerate PDF data (summary, title, short description) for all Files';

    /**
     * Execute the console command.
     */
    public function handle(FileController $controller)
    {
        $this->info('Starting PDF data regeneration...');

        // If you want to see progress for each file, you can:
        // 1) Add logging in the controller method itself, or
        // 2) Retrieve the files here and loop with $this->info()

        $controller->regeneratePdfData();

        $this->info('PDF data regeneration completed.');
    }
}
