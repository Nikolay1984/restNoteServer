<?php

/**
 * This class takes the request and splits it into parts.
 */
namespace http;


class Request
{
    public $requestMethod;
    public $requestUrn;
    public $requestBody=[];
    public $requestParam;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestUrn = $_SERVER["REQUEST_URI"];
        $this->requestParam = $_GET;
        if($_SERVER["REQUEST_METHOD"] == "GET" && $this->requestParam){
            $this->requestMethod = "GET_ID";
        }

        $this->getRequestBody();
    }

    /**
     * This method recognizes the presence in the request body, accepts and converts it from JSON to a string.
     * @return void
     */
    private function getRequestBody(){

        if($this->requestMethod === "POST" || $this->requestMethod === "PUT"){
            $this->requestBody = $this->normalizeRequestBody($this->getRawRequestBody());
        }

    }

    /**
     * Decode request body in php entity.
     * @param string $rawRequestBody
     * @return mixed
     */
    private function normalizeRequestBody($rawRequestBody){
     return json_decode($rawRequestBody);
    }

    /**
     * Getting the raw request body from the stream.
     * @return false|string
     */
    private function getRawRequestBody(){
        return file_get_contents("php://input");
    }

}