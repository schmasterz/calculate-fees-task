<?php
declare(strict_types=1);
namespace App\Service\Contract;

use App\Entity\Operation;

interface FeesCalculationInterface
{
    public function calculate(Operation $operation): float;

    public function supports(Operation $operation): bool;
}