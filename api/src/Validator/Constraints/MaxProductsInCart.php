<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MaxProductsInCart extends Constraint
{
    public string $message = 'You cannot have more than {{ limit }} different products in your cart.';
    public int $limit = 3;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}