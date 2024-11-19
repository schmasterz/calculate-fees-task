<?php
declare(strict_types=1);
namespace App\Entity;

class Operation
{
    private \DateTimeInterface $date;
    private int $userId;
    private string $userType;
    private string $type;
    private float $amount;
    private string $currency;
    private float $rate;
    public function __construct(
        \DateTimeInterface $date,
        int $userId,
        string $userType,
        string $type,
        float $amount,
        string $currency,
        float $rate,
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->rate = $rate;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): void
    {
        $this->userType = $userType;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
    public function getRate(): float
    {
        return $this->rate;
    }
}
