<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use CodeIgniter\Files\File;

/**
 * Stores menu-item images either in Cloudflare R2 (S3-compatible object storage)
 * or on the local filesystem.
 *
 * The driver is chosen automatically from the environment: when the R2_* variables
 * are present, images go to R2; otherwise they fall back to the local
 * public/uploads/menu_items directory so local development works without an R2
 * account. Only the object key (file name) is ever stored in the database.
 */
class MenuImageStorage
{
    private const LOCAL_DIR = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'menu_items';

    private bool $useR2;
    private ?S3Client $client = null;
    private string $bucket = '';
    private string $publicUrl = '';

    public function __construct()
    {
        $endpoint  = (string) env('R2_ENDPOINT', '');
        $bucket    = (string) env('R2_BUCKET', '');
        $accessKey = (string) env('R2_ACCESS_KEY_ID', '');
        $secretKey = (string) env('R2_SECRET_ACCESS_KEY', '');

        $this->useR2 = $endpoint !== '' && $bucket !== '' && $accessKey !== '' && $secretKey !== '';

        if ($this->useR2) {
            $this->bucket    = $bucket;
            $this->publicUrl = rtrim((string) env('R2_PUBLIC_URL', ''), '/');
            $this->client    = new S3Client([
                'version'                 => 'latest',
                'region'                  => 'auto',
                'endpoint'                => $endpoint,
                'use_path_style_endpoint' => true,
                'credentials'             => [
                    'key'    => $accessKey,
                    'secret' => $secretKey,
                ],
            ]);
        }
    }

    /**
     * Persists an uploaded file and returns the object key to store in the database.
     */
    public function save(File $file): string
    {
        $key = $file->getRandomName();

        if ($this->useR2) {
            $this->client->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $key,
                'Body'        => fopen($file->getRealPath(), 'rb'),
                'ContentType' => $file->getMimeType(),
            ]);

            return $key;
        }

        if (! is_dir(self::LOCAL_DIR)) {
            mkdir(self::LOCAL_DIR, 0775, true);
        }
        $file->move(self::LOCAL_DIR, $key);

        return $key;
    }

    /**
     * Removes a stored image. Missing files are ignored.
     */
    public function delete(?string $key): void
    {
        if ($key === null || $key === '') {
            return;
        }

        if ($this->useR2) {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);

            return;
        }

        $path = self::LOCAL_DIR . DIRECTORY_SEPARATOR . $key;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Returns the public URL used to display a stored image.
     */
    public function url(?string $key): string
    {
        if ($key === null || $key === '') {
            return '';
        }

        if ($this->useR2) {
            return $this->publicUrl . '/' . ltrim($key, '/');
        }

        return base_url('uploads/menu_items/' . $key);
    }
}
