<?php

namespace App\Support\Exports;

use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\Log;

trait HandlesExportPermissions
{
    protected static function fixExportPermissions(Export $export): void
    {
        $folder = storage_path('app/private/filament_exports/' . $export->getKey());
        if (!is_dir($folder)) return;

        try {
            @chmod($folder, 0775);
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($it as $item) {
                @chmod($item->getPathname(), $item->isDir() ? 0775 : 0664);
            }
        } catch (\Throwable $e) {
            Log::error('Export permission error: ' . $e->getMessage());
        }
    }
}
