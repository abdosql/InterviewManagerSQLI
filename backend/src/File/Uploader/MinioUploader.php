<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\File\Uploader;

use App\File\FileUploaderInterface;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

class MinioUploader implements FileUploaderInterface
{
    public function __construct(
        private S3Client $s3Client,
        #[Autowire(env: "MINIO_BUCKET")]
        private readonly string $bucketName,
        #[Autowire(env: 'BASE_DIR')]
        private string $baseDir,
    )
    {
    }

    public function upload(File $file): string
    {
        try {
            $key = $this->baseDir."/" . uniqid() . '.' . $file->guessExtension();
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
                'SourceFile' => $file->getPathname(),
                'ACL'    => 'public-read',
            ]);
            return $result->get('ObjectURL');
        }catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

    }
}