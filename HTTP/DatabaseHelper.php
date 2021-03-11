<?php
/**
 * This class interacts with the database.
 */

namespace http;


class DatabaseHelper
{
    private $pdo;

    /**
     * DatabaseHelper constructor.
     * Connects to the database and checks for a new connection.
     */
    public function __construct(){
        try {

            $this->pdo = new \PDO(DB_CONNECTION["dsn"],DB_CONNECTION["userName"],DB_CONNECTION["password"]);

        }catch (\PDOException $Exception){

            generationErrorApi('error_db_connection');
        }


    }

    /**
     * Gets a raw response.
     * @param string $methodRequest
     * @param array $requestBody
     * @param array $requestParam
     * @return mixed|\stdClass|string
     * @throws \Exception
     */
    public function getResponse($methodRequest,$requestBody,$requestParam){

        switch ($methodRequest){
            case "POST":
                $response = $this->tryTransaction($requestBody,[$this,'handlerPost'],'error_POST');
                break;

            case "GET":
                    $response = $this->tryTransaction(false,[$this,'handlerGet'],'error_GET');
                break;
            case "GET_ID":
                    $response = $this->tryTransaction($requestParam,[$this,'handlerGetId'],'error_GET_ID');
                break;
            case "PUT":
                $response = $this->tryTransaction($requestBody,[$this,'handlerPut'],'error_PUT');
                break;

            case "DELETE":
                $response = $this->tryTransaction($requestParam,[$this,'handlerDelete'],'error_DELETE');
                break;
            case "OPTIONS":
                $response = $this->handlerOptions();
                break;
            default:
                $response = $methodRequest ." not available";
        }
        return $response;
    }

    /**
     * Gets raw data from the POST request database.
     * @param array $requestBody
     * @return \stdClass
     * @throws \Exception
     */
    private function handlerPost($requestBody){
        $nameAlgorithm = $requestBody->name;
        $arrQueryParams = [$nameAlgorithm];
        $strQuery = "INSERT INTO algorithms(name) VALUE (?)";
        $this->query($strQuery,$arrQueryParams);


        $arrRecords = $requestBody->records;


        $resObj = new \stdClass();
        $resObj->name = $nameAlgorithm;
        $resObj->id = $this->pdo->lastInsertId();
        $resObj->records = $arrRecords;

        foreach ($arrRecords as $objRecord){
            $textRecord = $objRecord->text;
            $checkRecord =  $objRecord->done ? '1'  : '0' ;
            $idAlgorithm = $resObj->id;
            $arrQueryParams = [$idAlgorithm,$textRecord,$checkRecord];
            $strQuery = "INSERT INTO records(id_algorithm, text_data, done) VALUE (?, ?, ?) ";
            $this->query($strQuery,$arrQueryParams);

        };



       return $resObj;

    }

    /**
     * Gets raw data from the GET request database.
     * @return array|bool|false|\PDOStatement|string
     * @throws \Exception
     */
    private function handlerGet(){

            $strQuery = "SELECT id_algorithm id,name
                         FROM algorithms
            ";
            $res = $this->query($strQuery,[],\PDO::FETCH_ASSOC);


        return $res;


    }

    /**
     * Gets raw data from the GET request database with parameters URI.
     * @param array $requestParam
     * @return \stdClass
     * @throws \Exception
     */
    private function handlerGetId($requestParam){
        $id  = $requestParam['id'];
            $arrQueryParams = [$id];
        $strQueryRecords = "SELECT text_data text, CASE WHEN done = 0 THEN 'false' ELSE 'true' END AS done
                         FROM records
                         WHERE id_algorithm = ?";
        $arrOfRecords = $this->query($strQueryRecords,$arrQueryParams, \PDO::FETCH_ASSOC);

        $strQueryNameOfAlgorithm = "SELECT name 
                                    FROM algorithms
                                    WHERE id_algorithm = ?
        ";
        $nameOfAlgorithm = $this->query($strQueryNameOfAlgorithm,$arrQueryParams, \PDO::FETCH_ASSOC)[0]['name'];
        $resObj = new \stdClass();
        $resObj->name = $nameOfAlgorithm;
        $resObj->records = $arrOfRecords;
        $resObj->id = $id;
        return $resObj;
    }

    /**
     * Updates data in the database from a PUT request.
     * @param array $requestBody
     * @return void
     * @throws \Exception
     */
    private function handlerPut($requestBody){
        $nameAlgorithm = $requestBody->name;
        $idAlgorithm = $requestBody->id;
        $arrRecords = $requestBody->records;

        $strQueryUpdate = "UPDATE algorithms
                     SET name = ?
                     WHERE id_algorithm = ?";
        $arrQueryParamsUpdate = [$nameAlgorithm,$idAlgorithm];
         $this->query($strQueryUpdate,$arrQueryParamsUpdate);

        $strQueryDelete = "DELETE
                     FROM records
                     WHERE id_algorithm = ?";
        $arrQueryParamsDelete = [$idAlgorithm];
        $this->query($strQueryDelete, $arrQueryParamsDelete);

        foreach ($arrRecords as $objRecord){
            $textRecord = $objRecord->text;
            $checkRecord =  $objRecord->done ? '1'  : '0' ;
            $arrQueryParamsInsert = [$idAlgorithm, $textRecord, $checkRecord];
            $strQuery = "INSERT INTO records(id_algorithm, text_data, done) VALUE(?,?,?)";
            $this->query($strQuery, $arrQueryParamsInsert);
        };


    }

    /**
     * Removes data in a database from a DELETE request.
     * @param array $requestParam
     * @return int
     * @throws \Exception
     */
    private function handlerDelete($requestParam){

        $idAlgorithm =  $requestParam['id'];
        $arrQueryParams = [$idAlgorithm];
        $strQuery = "DELETE 
                     FROM algorithms 
                     WHERE id_algorithm = ?";
        $this->query($strQuery, $arrQueryParams, \PDO::FETCH_ASSOC);
        return $idAlgorithm;
    }

    /**
     * Returns data about the application API
     *
     */
    private function handlerOptions(){

    }

    /**
     * Wrapper over the PDO API.
     * @param string $strQuery
     * @param array $arrQueryParams
     * @param int $typeOutput
     * @return array|bool|false|\PDOStatement|string
     * @throws \Exception
     */
    private function query($strQuery,$arrQueryParams = [], $typeOutput = \PDO::FETCH_NUM){

            $res = $this->pdo->prepare($strQuery);
            $res->execute($arrQueryParams);

        if(!$res->errorInfo()[1]){
            $res  = $res->fetchAll($typeOutput);
            if(!$res){
                $res="";
            }
            return $res;
        };

        throw new \Exception($res->errorInfo()[2], $res->errorInfo()[0]);
    }

    /**
     * Perform operations on the database using a transaction.
     * @param array $requestBody
     * @param \Closure $handler
     * @param string $statusError
     * @return mixed
     * @throws \Exception
     */
    private function tryTransaction($requestBody,$handler,$statusError){
        try {
            $this->pdo->beginTransaction();
            $res =  $handler($requestBody);
            $this->pdo->commit();
            return $res;
        }catch (\Exception $e){
            $this->pdo->rollBack();
            generationErrorApi($statusError);
            throw $e;
        }


    }

}