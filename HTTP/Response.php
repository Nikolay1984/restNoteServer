<?php
/**
 * This class prepares a response to the client.
 */

namespace http;


class Response
{
    /**
     * Gets made response.
     * @param mixed $rawResponse
     * @param string $requestMethod
     * @return false|string
     */
    public function getResponse($rawResponse, $requestMethod){

        switch ($requestMethod){
            case "POST":
                $response = $this->handlerPost($rawResponse);
                break;

            case "GET":
                $response = $this->handlerGet($rawResponse);
                break;
            case "GET_ID":
                $response = $this->handlerGetId($rawResponse);
                break;
            case "PUT":
                $response = $this->handlerPut();
                break;

            case "DELETE":
                $response = $this->handlerDelete();
                break;
            case "OPTIONS":
                $response = $this->handlerOptions();
                break;
            default:
                $response = $this->serialize([
                    'status_code' => 501,
                    'status_text' => $rawResponse
                ]);
        }
        return $response;
    }

    /**
     * Prepared a response to a POST request.
     * @param mixed $rawResponse
     * @return array
     */
    private function handlerPost($rawResponse){

        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_POST'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_POST'][1],
            'data' => '',
            'headers' => ["Location: ". $_SERVER['SERVER_NAME']. "/?id=".$rawResponse->id]
        ];
    }

    /**
     * Prepared a response to a GET request.
     * @param mixed $rawResponse
     * @return array
     */
    private function handlerGet($rawResponse){

        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_GET'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_GET'][1],
            'data' => $this->serialize($rawResponse)
        ];
    }

    /**
     * Prepared a response to a GET request with an ID.
     * @param $rawResponse
     * @return array
     */
    private function handlerGetId($rawResponse){
        if(!$rawResponse->name){
            return [
                'status_code' => CODE_MESSAGE_SUCCESS['resource_NOT_FOUND'][0],
                'status_text' => CODE_MESSAGE_SUCCESS['resource_NOT_FOUND'][1],
                'data' => ''
            ];
        }
        $headers = ["Cache-Control: no-store, no-cache, must-revalidate",
                    "Pragma: no-cache"];
        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_GET_ID'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_GET_ID'][1],
            'data' => $this->serialize($rawResponse),
            'headers' => $headers
        ];
    }

    /**
     * Prepared a response to a PUT request.
     * @return array
     */
    private function handlerPut(){

        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_PUT'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_PUT'][1],
            'data' => ''
        ];
    }

    /**
     * Prepared a response to a DELETE request.
     * @return array
     */
    private function handlerDelete(){

        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_DELETE'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_DELETE'][1],
            'data' => ''
        ];
    }

    /**
     * Prepared a response to a OPTIONS request.
     * @return array
     */
    private function handlerOptions(){

        return [
            'status_code' => CODE_MESSAGE_SUCCESS['success_OPTIONS'][0],
            'status_text' => CODE_MESSAGE_SUCCESS['success_OPTIONS'][1],
            'data' => '',
            'headers' => ["Allow: GET, POST, PUT, DELETE, OPTIONS"]
        ];
    }

    /**
     * Encodes data to JSON.
     * @param mixed $data
     * @return false|string
     */
    private function serialize($data){
        return json_encode($data);
    }


}