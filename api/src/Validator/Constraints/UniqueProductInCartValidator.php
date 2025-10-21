<?php

namespace App\Validator\Constraints;

use App\Entity\CartItem;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueProductInCartValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueProductInCart) {
            throw new UnexpectedTypeException($constraint, UniqueProductInCart::class);
        }

        if (!$value instanceof CartItem) {
            throw new UnexpectedTypeException($value, CartItem::class);
        }

        if ($value->getId()) {
            return;
        }

        $cart = $value->cart;
        $product = $value->product;

        if (!$cart || !$product) {
            return;
        }

        foreach ($cart->cartItems as $existingItem) {
            if ($existingItem->product &&
                $existingItem->product->getId() === $product->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('product')
                    ->addViolation();
                return;
            }
        }
    }
}
