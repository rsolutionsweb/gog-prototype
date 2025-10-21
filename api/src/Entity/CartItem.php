<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Validator\Constraints\MaxProductsInCart;
use App\Validator\Constraints\UniqueProductInCart;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            openapi: new Model\Operation(
                summary: 'Add item to cart',
                description: 'Add a product to cart. Quantity is optional and defaults to 1. Maximum quantity is 10. You can have a maximum of 3 different products in your cart. Note: You cannot add the same product twice - use PATCH to update the quantity instead.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'quantity' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'default' => 1, 'example' => 1],
                                    'product' => ['type' => 'string', 'example' => '/products/1'],
                                    'cart' => ['type' => 'string', 'example' => '/carts/1']
                                ],
                                'required' => ['product', 'cart']
                            ]
                        ]
                    ])
                )
            )
        ),
        new Patch(
            openapi: new Model\Operation(
                summary: 'Update cart item quantity',
                description: 'Update the quantity of a cart item. Minimum is 1, maximum is 10.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/merge-patch+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'quantity' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'example' => 2]
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Delete()
    ]
)]
#[ORM\Entity]
#[MaxProductsInCart]
#[UniqueProductInCart]
class CartItem
{
    /** @var int|null  */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['cart:read'])]
    public ?int $id = null;

    /** @var int  */
    #[ORM\Column(options: ['default' => 1])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\LessThanOrEqual(10, message: 'You cannot add more than 10 items of the same product to your cart')]
    #[Groups(['cart:read'])]
    public int $quantity = 1;

    /** @var Product|null */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['cart:read'])]
    public ?Product $product = null;

    /** @var Cart|null  */
    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    public ?Cart $cart = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
