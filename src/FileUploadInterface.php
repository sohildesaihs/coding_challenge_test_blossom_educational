<?php


namespace Blossom\BackendDeveloperTest;

/**
 * Interface FileUploadInterface
 * @package Blossom\BackendDeveloperTest
 */
interface FileUploadInterface
{
    public function upload($file, $config);
}