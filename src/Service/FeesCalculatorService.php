<?php
declare(strict_types=1);
namespace App\Service;

use App\Entity\Operation;
use App\Exception\InvalidCalculatorException;

class FeesCalculatorService
{
    private iterable $calculators;
    public function __construct(iterable $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * @throws InvalidCalculatorException
     */
    public function calculate(Operation $operation): float
    {
        try {
            $calculator = current(
                array_filter(
                    iterator_to_array($this->calculators),
                    fn($calculator) => $calculator->supports($operation)
                )
            );
            return $calculator->calculate($operation);
        } catch (InvalidCalculatorException $e) {
            throw new InvalidCalculatorException($operation);
        }
    }
}