<?php

namespace App\Services;

class ImagesFinder
{
    public function findImages(string $dir, array $extensions = ['png', 'jpg', 'jpeg']): array
    {
        $allFiles = $this->scanDirRecursive($dir);
        $extensionsRegex = sprintf('/\.(%s)$/i', implode('|', $extensions));
        return array_filter($allFiles, function ($fileName) use ($extensionsRegex) {
            return preg_match($extensionsRegex, $fileName);
        });
    }

    private function scanDirRecursive(string $dir, array $results = []): array
    {
        $dirContent = array_diff(scandir($dir), ['.', '..']);
        foreach ($dirContent as $fileName) {
            $path = $dir . '/' . $fileName;
            if (is_file($path) && is_readable($path)) {
                $results[] = $path;
            } elseif (is_dir($path) && is_readable($path)) {
                $results = $this->scanDirRecursive($path, $results);
            }
        }
        return $results;
    }
}