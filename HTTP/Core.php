<?php
/**
 * This class is the base engine of the application.
 */
namespace http;


class Core
{
    /**
     * This container contains all the service providers for the application.
     * @var array
     */
    public $serviceContainer = [];

    /**
     * Contains the serialized response from the server.
     * @var string
     */
    private $response;

    /**
     * Core constructor.
     * Create a new core instance.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->serviceContainer['request'] = $request;
        $this->serviceContainer['databasehelper'] = new DatabaseHelper();
        $this->serviceContainer['response'] = new Response();
        $this->serviceContainer['gatekeeper'] = new Gatekeeper();

        $this->secureInputData();
        $this->createResponse();
    }

    /**
     *Neutralize potential hazard to BD in user request.
     * @return void
     */
    private function secureInputData(){

        if($this->serviceContainer['request']->requestUrn){
            $this->serviceContainer['request']->requestUrn = $this->serviceContainer['gatekeeper']->secureString($this->serviceContainer['request']->requestUrn);
        }
        if($this->serviceContainer['request']->requestBody){
            $this->serviceContainer['request']->requestBody = $this->serviceContainer['gatekeeper']->secureBody($this->serviceContainer['request']->requestBody);
        }

    }

    /**
     * Receiving a raw response from the database and preparing it for sending to the client.
     * @return void
     */
    private function createResponse(){
        $request = $this->serviceContainer['request'];
        $rawResponse = $this->serviceContainer['databasehelper']->getResponse($request->requestMethod, $request->requestBody,$request->requestParam);

        $this->response =  $this->serviceContainer['response']->getResponse($rawResponse,  $this->serviceContainer['request']->requestMethod);

    }

    /**
     * Sets the headers and sends a ready response to the client.
     * @return void
     */
    public function send(){
        $stringResponse = "HTTP/1.0 {$this->response['status_code']} {$this->response['status_text']}";

        header($stringResponse);
        if(array_key_exists('headers',$this->response)){
            foreach ($this->response['headers'] as $header){
                header($header);
            }
        }
        echo $this->response['data'];

    }

}