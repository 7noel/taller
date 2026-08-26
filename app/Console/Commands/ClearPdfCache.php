<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearPdfCache extends Command
{
    protected $signature = 'pdf:clear';

    protected $description = 'Elimina todos los PDFs de inventario cacheados en storage (los próximos se regeneran).';

    public function handle(): int
    {
        $disk = Storage::disk(config('pdf.disk'));
        $directory = config('pdf.directory');

        $files = $disk->files($directory);

        $disk->deleteDirectory($directory);

        $disk->makeDirectory($directory);
        $disk->put($directory.'/.gitignore', "*\n!.gitignore\n");

        $this->info('Caché de PDFs limpiada: '.count($files).' archivo(s) eliminado(s).');

        return self::SUCCESS;
    }
}
