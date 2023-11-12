<?php

namespace App\Data;

class Person
{
    private ?string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function sayHello(): string
    {
        return "Hello " . $this->name;
    }
}