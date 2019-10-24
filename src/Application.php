<?php

namespace Blossom\BackendDeveloperTest;

use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EncodingStub\Client as EncodeClient;
use FFMPEGStub\FFMPEG;
use Blossom\BackendDeveloperTest\UploadFactory;

/**
 * You should implement this class however you want.
 *
 * The only requirement is existence of public function `handleRequest()`
 * as this is what is tested. The constructor's signature must not be changed.
 */
class Application
{
    private $_configParams;

    /**
     * By default the constructor takes a single argument which is a config array.
     *
     * You can handle it however you want.
     *
     * @param array $config Application config.
     */
    public function __construct(array $config)
    {
        $this->_configParams = $config;
    }

    /**
     * This method should handle a Request that comes pre-filled with various data.
     *
     * You should implement it however you want and it should return a Response
     * that passes all tests found in EncoderTest.
     *
     * @param Request $request The request.
     *
     * @return Response
     */
    public function handleRequest(Request $request): Response
    {
        $response = new Response();
        $response->setCharset('UTF-8');
        $response->headers->set('Content-Type', 'application/json');

        // Request type must be POST
        if (!$request->isMethod('post')) {
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)->setContent(json_encode(["message" => "Unsupported request type."]));
            return $response;
        }

        // Upload key must exist
        if (!$request->get("upload")) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent(json_encode(["message" => "Please select file upload destination."]));
            return $response;
        }

        // File must be selected to be uploaded.
        $file = $request->files->get("file");
        if (empty($file)) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent(json_encode(["message" => "Please select a file to upload."]));
            return $response;
        }

        // upload file
        try {
            $bucketType = $request->get("upload");
            $fileUploadFactory = new UploadFactory($this->_configParams[$bucketType]);
            $url = $fileUploadFactory->uploadFile($file, $bucketType);

            // File encoding validations.
            $formats = null;
            if (!empty($reqFormats = $request->get("formats"))) {
                $formats = $this->fileEncode($file, $reqFormats, $request->get("upload"), $fileUploadFactory);
            }
        } catch (\Exception $ex) {
            // file upload exception messages.
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent(json_encode(["message" => $ex->getMessage()]));
            return $response;
        }

        $content = array("url" => $url, "formats" => $formats);
        $response->setContent(json_encode($content))->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Performs file encoding for supported types.
     *
     * @param $file
     * @param $reqFormats
     * @param $uploadBucket
     * @return array
     * @throws \Exception
     */
    private function fileEncode($file, $reqFormats, $uploadBucket, $fileUploadFactory)
    {
        $return = [];
        foreach ($reqFormats as $reqFormat) {
            switch ($reqFormat) {
                case "mp4":
                    $mp4Convertor = new FFMPEG();
                    $file = $mp4Convertor->convert($file);
                    // upload again converted file.
                    $return[$reqFormat] = $fileUploadFactory->uploadFile($file, $uploadBucket);
                    break;
                case "gif":
                    throw new \Exception("Unsupported file format provided for conversion.");
                    break;
                default:
                    $encodingParams = $this->_configParams["encoding.com"];
                    $encode = new EncodeClient($encodingParams["app_id"], $encodingParams["access_token"]);
                    $return[$reqFormat] = $encode->encodeFile($file, $reqFormat);
                    break;
            }
        }

        return $return;
    }
}
