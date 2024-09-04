<?php

namespace App\Services;

use CodeIgniter\Files\File;
use CodeIgniter\Images\Handlers\GDHandler;
use Config\Images as ImagesConfig;

class ImageService
{
    protected $config;
    protected $imageHandler;

    public function __construct(ImagesConfig $config, GDHandler $imageHandler)
    {
        $this->config = $config;
        $this->imageHandler = $imageHandler;
    }

    public function validateImage(?File $file, string $uploadType)
    {
        if ($file === null) {
            throw new \RuntimeException('No file selected');
        }

        if (!$file->isValid()) {
            throw new \RuntimeException($file->getErrorString());
        }

        $maxSize = $this->config->maxSizes[$uploadType] ?? $this->config->defaultMaxSize;
        if ($file->getSizeByUnit('mb') > $maxSize) {
            throw new \RuntimeException("File too large. Max size is {$maxSize}MB");
        }

        if (!in_array($file->getMimeType(), $this->config->allowedTypes)) {
            throw new \RuntimeException('Invalid file type. Only JPEG and PNG allowed');
        }
    }

    public function saveImage(File $file, string $uploadType)
    {
        $uploadPath = $this->config->uploadPaths[$uploadType] ?? $this->config->defaultUploadPath;
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $imagePath = $uploadPath . $newName;

        list($width, $height) = getimagesize($imagePath);
        $this->config->defaultThumbnailSize = [$width, $height];

        $thumbnailSize = $this->config->thumbnailSizes[$uploadType] ?? $this->config->defaultThumbnailSize;

        $this->imageHandler
            ->withFile($imagePath)
            ->fit($thumbnailSize[0], $thumbnailSize[1], 'center')
            ->save($imagePath);

        return $newName;
    }

    public function deleteImage(?string $filename, string $uploadType)
    {
        if ($filename) {
            $uploadPath = $this->config->uploadPaths[$uploadType] ?? $this->config->defaultUploadPath;
            $fullPath = $uploadPath . $filename;
            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    public function outputImage(string $filename, string $uploadType)
    {
        $uploadPath = $this->config->uploadPaths[$uploadType] ?? $this->config->defaultUploadPath;
        $fullPath = $uploadPath . $filename;
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($fullPath);

        header("Content-Type: $type");
        header("Content-Length: " . filesize($fullPath));

        readfile($fullPath);
        exit;
    }
}