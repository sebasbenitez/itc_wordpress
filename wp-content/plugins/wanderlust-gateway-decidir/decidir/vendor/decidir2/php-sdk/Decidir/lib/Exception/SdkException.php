<?php
namespace Decidir\Exception;

class SdkException extends \Exception
{   
    protected $data;
    protected $message;
    protected $code;
    protected $previous;

    public function __construct($message, $code = 0, $data, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
        if($message == 'not_found_error'){
          $this->datas = 'Tarjeta Invalida';
        } else {
          $this->datas = $message;
        }
        
    }

    public function getData(){
        return $this->data;
    }    
    public function getDatas(){
        return $this->datas;
    }
}