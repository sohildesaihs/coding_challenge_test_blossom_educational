<?php


namespace Blossom\BackendDeveloperTest;

use S3Stub\FileObject;
use Blossom\BackendDeveloperTest\Upload\Dropbox;
use Blossom\BackendDeveloperTest\Upload\FTP;
use Blossom\BackendDeveloperTest\Upload\S3;

/**
 * Class UploadFactory
 * @package Blossom\BackendDeveloperTest
 */
class UploadFactory
{
    private $_config = null;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Validate and filter upload files to relevant storage.
     *
     * @param $file FileObject
     * @param $type string
     * @return string
     * @throws \Exception
     */
    public function uploadFile($file, $bucketType)
    {
        $uploadFileUrl = null;
        switch ($bucketType) {
            case UploadBuckets::FTP:
                $factory = new FTP();
                break;
            case UploadBuckets::S3:
                $factory = new S3();
                break;
            case UploadBuckets::DROPBOX:
                $factory = new Dropbox();
                break;
            default:
                throw new \Exception("Unknown upload bucket selected.");
                break;
        }

        return $factory->upload($file, $this->_config);
    }
}