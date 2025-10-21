<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['cart:read']],
    operations: [
        new Get(
            openapi: new Model\Operation(
                summary: 'Retrieve a cart',
                description: 'Get a cart with all its items and related product information',
                responses: [
                    '200' => [
                        'description' => 'Cart resource',
                        'content' => [
                            'application/ld+json' => [
                                'example' => [
                                    '@context' => '/api/contexts/Cart',
                                    '@id' => '/api/carts/1',
                                    '@type' => 'Cart',
                                    'id' => 1,
                                    'totalPrice' => '139.97',
                                    'cartItems' => [
                                        [
                                            '@id' => '/api/cart_items/1',
                                            '@type' => 'CartItem',
                                            'id' => 1,
                                            'quantity' => 2,
                                            'product' => [
                                                '@id' => '/api/products/1',
                                                '@type' => 'Product',
                                                'id' => 1,
                                                'title' => 'The Witcher 3: Wild Hunt',
                                                'price' => 39.99
                                            ]
                                        ],
                                        [
                                            '@id' => '/api/cart_items/2',
                                            '@type' => 'CartItem',
                                            'id' => 2,
                                            'quantity' => 1,
                                            'product' => [
                                                '@id' => '/api/products/2',
                                                '@type' => 'Product',
                                                'id' => 2,
                                                'title' => 'Cyberpunk 2077',
                                                'price' => 59.99
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Post(
            openapi: new Model\Operation(
                summary: 'Create a new cart',
                description: 'Creates an empty cart. Add items using the CartItem resource.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => []
                            ],
                            'example' => []
                        ]
                    ])
                )
            )
        ),
        new Delete()
    ]
)]
#[ORM\Entity]
class Cart
{
    /** @var int */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['cart:read'])]
    public ?int $id = null;

    /** @var CartItem[] */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart', cascade: ['persist', 'remove'])]
    #[Groups(['cart:read'])]
    public iterable $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ApiProperty(readable: true, writable: false)]
    #[Groups(['cart:read'])]
    public function getTotalPrice(): string
    {
        $total = 0.0;

        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->product && $cartItem->product->price) {
                $itemPrice = (float) $cartItem->product->price;
                $total += $itemPrice * $cartItem->quantity;
            }
        }

        return number_format($total, 2, '.', '');
    }
}
