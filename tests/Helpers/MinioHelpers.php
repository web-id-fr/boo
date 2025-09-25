<?php

namespace Tests\Helpers;

use Aws\S3\S3Client;

trait MinioHelpers
{
    const TEST_BUCKET_NAME = 'backup-test';

    private function getS3Client(): S3Client
    {
        return new S3Client([
            'region' => 'us-east-1',
            'endpoint' => 'http://minio:9000',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => 'minio',
                'secret' => 'password',
            ],
        ]);
    }

    protected function createTestBucket(): void
    {
        $this->getS3Client()->createBucket(['Bucket' => self::TEST_BUCKET_NAME]);
    }

    protected function deleteTestBucket(): void
    {
        $s3 = $this->getS3Client();

        $objects = $s3->listObjectVersions(['Bucket' => self::TEST_BUCKET_NAME]);

        if (isset($objects['Versions']) && count($objects['Versions']) > 0) {
            $objectsToDelete = [];
            foreach ($objects['Versions'] as $version) {
                $objectsToDelete[] = [
                    'Key' => $version['Key'],
                    'VersionId' => $version['VersionId']
                ];
            }

            $chunks = array_chunk($objectsToDelete, 1000);
            foreach ($chunks as $chunk) {
                $s3->deleteObjects([
                    'Bucket' => self::TEST_BUCKET_NAME,
                    'Delete' => [
                        'Objects' => $chunk
                    ]
                ]);
            }
        }

        if (isset($objects['DeleteMarkers']) && count($objects['DeleteMarkers']) > 0) {
            $markersToDelete = [];
            foreach ($objects['DeleteMarkers'] as $marker) {
                $markersToDelete[] = [
                    'Key' => $marker['Key'],
                    'VersionId' => $marker['VersionId']
                ];
            }

            $chunks = array_chunk($markersToDelete, 1000);
            foreach ($chunks as $chunk) {
                $s3->deleteObjects([
                    'Bucket' => self::TEST_BUCKET_NAME,
                    'Delete' => [
                        'Objects' => $chunk
                    ]
                ]);
            }
        }

        $s3->deleteBucket(['Bucket' => self::TEST_BUCKET_NAME]);
    }

    protected function doesBucketExist(): bool
    {
        return $this->getS3Client()->doesBucketExist(self::TEST_BUCKET_NAME);
    }

    protected function findFilesByDateTimePattern(string $directory = 'backups'): array
    {
        $prefix = rtrim($directory, '/') . '/';
        $matchingFiles = [];

        $objects = $this->getS3Client()->listObjectsV2([
            'Bucket' => self::TEST_BUCKET_NAME,
            'Prefix' => $prefix
        ]);

        $regexPattern = '/^\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}\.zip$/';

        foreach ($objects['Contents'] as $object) {
            $filename = basename($object['Key']);

            // Check if filename matches the pattern
            if (preg_match($regexPattern, $filename)) {
                $matchingFiles[] = [
                    'key' => $object['Key'],
                    'last_modified' => $object['LastModified'],
                    'size' => $object['Size']
                ];
            }
        }

        return $matchingFiles;
    }
}
