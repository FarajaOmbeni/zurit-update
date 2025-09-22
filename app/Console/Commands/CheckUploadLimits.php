<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUploadLimits extends Command
{
    protected $signature = 'check:upload-limits';
    protected $description = 'Check current PHP upload limits and configurations';

    public function handle()
    {
        $this->info('Current PHP Upload Configuration:');
        $this->newLine();

        $limits = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ];

        foreach ($limits as $setting => $value) {
            $this->line("<comment>{$setting}:</comment> <info>{$value}</info>");
        }

        $this->newLine();
        
        // Convert to bytes for comparison
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        $recommendedSize = 300 * 1024 * 1024; // 300MB

        if ($uploadMax < $recommendedSize || $postMax < $recommendedSize) {
            $this->warn('⚠️  Current limits may be too low for large video uploads (recommended: 300MB+)');
            $this->line('');
            $this->line('To increase limits, edit your php.ini file and set:');
            $this->line('<comment>upload_max_filesize = 300M</comment>');
            $this->line('<comment>post_max_size = 300M</comment>');
            $this->line('<comment>max_execution_time = 600</comment>');
            $this->line('<comment>max_input_time = 600</comment>');
            $this->line('<comment>memory_limit = 512M</comment>');
            $this->line('');
            $this->line("PHP ini file location: <info>" . php_ini_loaded_file() . "</info>");
        } else {
            $this->info('✅ Upload limits are sufficient for large video files');
        }

        return 0;
    }

    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }
}