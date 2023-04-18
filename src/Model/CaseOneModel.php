<?php

namespace App\Model;

class CaseOneModel
{

    private $property = "1";

    private $propertyTwo = 2;

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     */
    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    /**
     * @return int
     */
    public function getPropertyTwo(): int
    {
        return $this->propertyTwo;
    }

    /**
     * @param int $propertyTwo
     */
    public function setPropertyTwo(int $propertyTwo): void
    {
        $this->propertyTwo = $propertyTwo;
    }
}