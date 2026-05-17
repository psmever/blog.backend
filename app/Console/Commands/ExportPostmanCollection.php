<?php

namespace App\Console\Commands;

use App\Services\Postman\PostmanCollectionExporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportPostmanCollection extends Command
{
    protected $signature = 'postman:export {--output= : Output path for the Postman collection JSON}';

    protected $description = 'Export API routes as a Postman collection';

    public function handle(PostmanCollectionExporter $exporter): int
    {
        $output = $this->normalizeOutputPath($this->option('output'));

        File::ensureDirectoryExists(dirname($output));
        File::put($output, $exporter->toJson());

        $this->info(sprintf('Postman collection exported: %s', $output));

        return self::SUCCESS;
    }

    private function normalizeOutputPath(mixed $output): string
    {
        if (is_string($output) && trim($output) !== '') {
            return $output;
        }

        return storage_path('app/postman/blog-backend.postman_collection.json');
    }
}
