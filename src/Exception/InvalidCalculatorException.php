<?php
namespace App\Exception;

use Exception;
use RuntimeException;
use App\Entity\Operation;

class InvalidCalculatorException extends RuntimeException
{
    private Operation $operation;

    public function __construct(Operation $operation, $code = 0, Exception $previous = null)
    {
        $this->operation = $operation;
        $message = "No suitable calculator found for operation type: " . $this->operation->getType();

        parent::__construct($message, $code, $previous);
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }
}
