<?php

namespace App\Console\Commands;

use App\Models\PostImage;
use App\Services\PostImageThumbnailService;
use Illuminate\Console\Command;
use Throwable;

class BackfillPostImageThumbnails extends Command
{
    protected $signature = 'posts:backfill-thumbnails {--chunk=100 : Number of images to process per chunk}';

    protected $description = 'Generate missing post image thumbnails';

    public function handle(PostImageThumbnailService $thumbnails): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $created = 0;
        $skipped = 0;
        $failed = 0;

        try {
            $thumbnails->ensureSupported();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        PostImage::query()
            ->with('thumbnailVariant')
            ->orderBy('id')
            ->chunkById($chunk, function ($images) use ($thumbnails, &$created, &$skipped, &$failed): void {
                foreach ($images as $image) {
                    if ($image->thumbnailVariant) {
                        $skipped++;

                        continue;
                    }

                    try {
                        $thumbnails->createForImage($image);
                        $created++;
                    } catch (Throwable $e) {
                        $failed++;
                        $this->error(sprintf('%s: %s', $image->path, $e->getMessage()));
                    }
                }
            });

        $this->info(sprintf(
            'Post image thumbnail backfill completed: created=%d, skipped=%d, failed=%d',
            $created,
            $skipped,
            $failed
        ));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
