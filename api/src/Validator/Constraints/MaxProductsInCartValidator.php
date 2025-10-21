<?php

namespace App\Validator\Constraints;

use App\Entity\CartItem;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MaxProductsInCartValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MaxProductsInCart) {
            throw new UnexpectedTypeException($constraint, MaxProductsInCart::class);
        }

        if (!$value instanceof CartItem) {
            throw new UnexpectedTypeException($value, CartItem::class);
        }

        $cart = $value->cart;
        if (!$cart) {
            return;
        }

        $newProduct = $value->product;
        if (!$newProduct) {
            return;
        }

        $distinctProductIds = [];

        foreach ($cart->cartItems as $cartItem) {
            if ($value->getId() && $cartItem->getId() === $value->getId()) {
                continue;
            }

            $product = $cartItem->product;
            if ($product && $product->getId()) {
                $distinctProductIds[$product->getId()] = true;
            }
        }

        $newProductId = $newProduct->getId();
        if (!isset($distinctProductIds[$newProductId])) {
            $currentUniqueProductCount = count($distinctProductIds);
            $totalUniqueProducts = $currentUniqueProductCount + 1;

            if ($totalUniqueProducts > $constraint->limit) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ limit }}', (string) $constraint->limit)
                    ->addViolation();
            }
        }
    }
}
