<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CartTest extends ApiTestCase
{
    public function testCreateCart(): void
    {
        static::createClient()->request('POST', '/carts', [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Cart',
            '@type' => 'Cart',
            'totalPrice' => '0.00',
        ]);
    }

    public function testGetCart(): void
    {
        $client = static::createClient();

        // Create a cart
        $response = $client->request('POST', '/carts', [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $cartData = $response->toArray();

        // Get the cart
        $client->request('GET', $cartData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Cart',
            '@type' => 'Cart',
            'totalPrice' => '0.00',
            'cartItems' => [],
        ]);
    }

    public function testDeleteCart(): void
    {
        $client = static::createClient();

        // Create a cart
        $response = $client->request('POST', '/carts', [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $cartData = $response->toArray();

        // Delete the cart
        $client->request('DELETE', $cartData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify cart is deleted
        $client->request('GET', $cartData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCartWithItems(): void
    {
        $client = static::createClient();

        // Create a product
        $productResponse = $client->request('POST', '/products', [
            'json' => [
                'title' => 'Test Game',
                'price' => '29.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $productData = $productResponse->toArray();

        // Create a cart
        $cartResponse = $client->request('POST', '/carts', [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $cartData = $cartResponse->toArray();

        // Add item to cart
        $client->request('POST', '/cart_items', [
            'json' => [
                'quantity' => 2,
                'product' => $productData['@id'],
                'cart' => $cartData['@id'],
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        // Get cart with items
        $cartWithItemsResponse = $client->request('GET', $cartData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $cartWithItems = $cartWithItemsResponse->toArray();

        $this->assertJsonContains([
            '@context' => '/contexts/Cart',
            '@type' => 'Cart',
            'totalPrice' => '59.98', // 2 * 29.99
        ]);

        // Verify cart items are embedded
        $this->assertIsArray($cartWithItems['cartItems']);
        $this->assertCount(1, $cartWithItems['cartItems']);
        $this->assertEquals(2, $cartWithItems['cartItems'][0]['quantity']);
    }

    /**
     * Complete multi-step workflow test:
     * 1. Create cart
     * 2. Create product
     * 3. Create cart item
     * 4. Verify cart with embedded data
     */
    public function testCompleteCartWorkflow(): void
    {
        $client = static::createClient();

        // Step 1: Create a cart
        $cartResponse = $client->request('POST', '/carts', [
            'json' => [],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $cartData = $cartResponse->toArray();

        $this->assertArrayHasKey('@id', $cartData);
        $this->assertArrayHasKey('id', $cartData);
        $this->assertEquals('0.00', $cartData['totalPrice']);
        $this->assertIsArray($cartData['cartItems']);
        $this->assertEmpty($cartData['cartItems']);

        // Step 2: Create a product
        $productResponse = $client->request('POST', '/products', [
            'json' => [
                'title' => 'The Witcher 3: Wild Hunt',
                'price' => '39.99',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $productData = $productResponse->toArray();

        $this->assertArrayHasKey('@id', $productData);
        $this->assertArrayHasKey('id', $productData);
        $this->assertEquals('The Witcher 3: Wild Hunt', $productData['title']);
        $this->assertEquals('39.99', $productData['price']);
        $this->assertEquals('USD', $productData['currency']);

        // Step 3: Create a cart item
        $cartItemResponse = $client->request('POST', '/cart_items', [
            'json' => [
                'quantity' => 2,
                'product' => $productData['@id'],
                'cart' => $cartData['@id'],
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $cartItemData = $cartItemResponse->toArray();

        $this->assertArrayHasKey('@id', $cartItemData);
        $this->assertArrayHasKey('id', $cartItemData);
        $this->assertEquals(2, $cartItemData['quantity']);
        $this->assertEquals($productData['@id'], $cartItemData['product']);
        $this->assertEquals($cartData['@id'], $cartItemData['cart']);

        // Step 4: Verify cart with embedded data
        $finalCartResponse = $client->request('GET', $cartData['@id'], [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $finalCartData = $finalCartResponse->toArray();

        // Verify cart has the correct total price
        $this->assertEquals('79.98', $finalCartData['totalPrice']); // 2 * 39.99

        // Verify cart items are embedded
        $this->assertIsArray($finalCartData['cartItems']);
        $this->assertCount(1, $finalCartData['cartItems']);

        // Verify cart item details
        $cartItem = $finalCartData['cartItems'][0];
        $this->assertEquals(2, $cartItem['quantity']);
        $this->assertArrayHasKey('product', $cartItem);

        // Verify product is embedded in cart item
        $product = $cartItem['product'];
        $this->assertEquals('The Witcher 3: Wild Hunt', $product['title']);
        $this->assertEquals('39.99', $product['price']);
        $this->assertEquals('USD', $product['currency']);
    }
}