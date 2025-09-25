<?php

namespace Tests\Feature;

use Tests\Helpers\MinioHelpers;
use Tests\TestCase;

class SelfBackupTest extends TestCase
{
    use MinioHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->doesBucketExist()) {
            $this->deleteTestBucket();
        }

        $this->createTestBucket();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteTestBucket();
    }

    public function test_the_application_can_backup_itself_source_code_to_minio_s3_bucket()
    {
        $this->artisan('backup:run --only-files --disable-notifications')->assertSuccessful();

        $this->assertCount(1, $this->findFilesByDateTimePattern(), 'Backup zip file not found');
    }
}
