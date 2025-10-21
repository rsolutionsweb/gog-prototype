<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ProductTest extends ApiTestCase
{
    public function testCreateProduct(): void
    {
        static::createClient()->request('POST', '/products', [
            'json' => [
                'title' => 'Dark Souls III',
                'price' => '39.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@type' => 'Product',
            'title' => 'Dark Souls III',
            'price' => '39.99',
            'currency' => 'USD',
        ]);
    }

    public function testCreateProductWithDuplicateTitle(): void
    {
        $client = static::createClient();

        // Create first product
        $client->request('POST', '/products', [
            'json' => [
                'title' => 'Cyberpunk 2077',
                'price' => '59.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        // Try to create duplicate
        $client->request('POST', '/products', [
            'json' => [
                'title' => 'Cyberpunk 2077',
                'price' => '49.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@type' => 'ConstraintViolation',
            'title' => 'An error occurred',
        ]);
    }

    public function testGetProduct(): void
    {
        $client = static::createClient();

        // Create a product
        $response = $client->request('POST', '/products', [
            'json' => [
                'title' => 'Baldurs Gate 3',
                'price' => '49.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $productData = $response->toArray();

        // Get the product
        $client->request('GET', $productData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@type' => 'Product',
            'title' => 'Baldurs Gate 3',
            'price' => '49.99',
            'currency' => 'USD',
        ]);
    }

    public function testGetProducts(): void
    {
        $client = static::createClient();

        // Create multiple products
        $client->request('POST', '/products', [
            'json' => [
                'title' => 'Elden Ring',
                'price' => '59.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $client->request('POST', '/products', [
            'json' => [
                'title' => 'Starfield',
                'price' => '69.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        // Get all products
        $client->request('GET', '/products', [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@type' => 'Collection',
        ]);
    }
}