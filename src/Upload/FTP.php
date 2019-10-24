<?php


namespace Blossom\BackendDeveloperTest\Upload;


use Blossom\BackendDeveloperTest\FileUploadInterface;
use FTPStub\FTPUploader;
use S3Stub\FileObject;

/**
 * Class FTP
 * @package Blossom\BackendDeveloperTest\Upload
 */
class FTP implements FileUploadInterface
{
    /**
     * @param $file FileObject
     * @return string
     * @throws \Exception
     */
    public function upload($file, $config)
    {
        $host = $config["hostname"];
        $user = $config["username"];
        $password = $config["password"];
        $folder = $config["destination"];

        $ftp = new FTPUploader($file, $host, $user, $password, $folder);

        if (!$ftp) {
            throw new \Exception("FTP upload error occurred.");
        }

        return "ftp://$host/$folder/" . $file->getFileName();
    }
}