<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueProductInCart extends Constraint
{
    public string $message = 'This product is already in the cart. Please update the quantity of the existing cart item instead.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}