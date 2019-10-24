<?php


namespace Blossom\BackendDeveloperTest\Upload;


use Blossom\BackendDeveloperTest\FileUploadInterface;
use S3Stub\Client;
use S3Stub\FileObject;

/**
 * Class S3
 * @package Blossom\BackendDeveloperTest\Upload
 */
class S3 implements FileUploadInterface
{
    /**
     * Upload file on S3 bucket
     *
     * @param $file FileObject
     * @return string
     * @throws \Exception
     */
    public function upload($file, $config)
    {
        $s3 = new Client($config["access_key_id"], $config["secret_access_key"]);

        $uploadedFile = $s3->send($file, $config["bucketname"]);

        return $uploadedFile->getPublicUrl();
    }
}