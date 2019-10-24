<?php


namespace Blossom\BackendDeveloperTest\Upload;


use Blossom\BackendDeveloperTest\FileUploadInterface;
use DropboxStub\DropboxClient;
use S3Stub\FileObject;

class Dropbox implements FileUploadInterface
{

    /**
     * Upload file on Dropbox
     *
     * @param $file FileObject
     * @return string
     * @throws \Exception
     */
    public function upload($file, $config)
    {
        $dropbox = new DropboxClient($config["access_key"], $config["secret_token"], $config["container"]);

        // todo: handle exceptions

        return $dropbox->upload($file);
    }
}