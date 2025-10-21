<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    paginationItemsPerPage: 3,
    operations: [
        new Get(
            openapi: new Model\Operation(
                summary: 'Retrieve a product',
                description: 'Get a product with information about which carts contain it',
                responses: [
                    '200' => [
                        'description' => 'Product resource',
                        'content' => [
                            'application/ld+json' => [
                                'example' => [
                                    '@context' => '/api/contexts/Product',
                                    '@id' => '/api/products/1',
                                    '@type' => 'Product',
                                    'id' => 1,
                                    'title' => 'The Witcher 3: Wild Hunt',
                                    'price' => '39.99',
                                    'currency' => 'USD',
                                    'cartItems' => [
                                        '/api/cart_items/1',
                                        '/api/cart_items/5'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new GetCollection(
            openapi: new Model\Operation(
                summary: 'Retrieve all products',
                description: 'Get a collection of all available products',
                responses: [
                    '200' => [
                        'description' => 'Product collection',
                        'content' => [
                            'application/ld+json' => [
                                'example' => [
                                    '@context' => '/api/contexts/Product',
                                    '@id' => '/api/products',
                                    '@type' => 'hydra:Collection',
                                    'hydra:totalItems' => 3,
                                    'hydra:member' => [
                                        [
                                            '@id' => '/api/products/1',
                                            '@type' => 'Product',
                                            'id' => 1,
                                            'title' => 'The Witcher 3: Wild Hunt',
                                            'price' => '39.99',
                                            'currency' => 'USD',
                                            'cartItems' => ['/api/cart_items/1']
                                        ],
                                        [
                                            '@id' => '/api/products/2',
                                            '@type' => 'Product',
                                            'id' => 2,
                                            'title' => 'Cyberpunk 2077',
                                            'price' => '59.99',
                                            'currency' => 'USD',
                                            'cartItems' => ['/api/cart_items/2']
                                        ],
                                        [
                                            '@id' => '/api/products/3',
                                            '@type' => 'Product',
                                            'id' => 3,
                                            'title' => 'Baldurs Gate 3',
                                            'price' => '49.99',
                                            'currency' => 'USD',
                                            'cartItems' => []
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
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => ['type' => 'string', 'example' => 'The Witcher 3: Wild Hunt'],
                                    'price' => ['type' => 'string', 'example' => '39.99']
                                ]
                            ]
                        ]
                    ])
                )
            )
        )
    ]
)]
#[ORM\Entity]
#[UniqueEntity(fields: ['title'], message: 'A product with this title already exists.')]
class Product
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['cart:read'])]
    public ?int $id = null;

    /**
     * @var string
     */
    #[ORM\Column(unique: true)]
    #[Groups(['cart:read'])]
    public string $title;

    /**
     * @var string
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['cart:read'])]
    public string $price;

    /**
     * @var string
     */
    #[ORM\Column(length: 3, options: ['default' => 'USD'])]
    #[ApiProperty(writable: false)]
    #[Groups(['cart:read'])]
    public string $currency = 'USD';

    /** @var CartItem[] */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product')]
    public iterable $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
