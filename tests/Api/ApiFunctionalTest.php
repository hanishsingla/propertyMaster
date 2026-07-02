<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiFunctionalTest extends WebTestCase
{
    private function json(KernelBrowser $client): array
    {
        return json_decode($client->getResponse()->getContent(), true) ?? [];
    }

    private function csrf(KernelBrowser $client): string
    {
        $client->request('GET', '/api/csrf');
        return $this->json($client)['token'];
    }

    private function post(KernelBrowser $client, string $url, array $body, ?string $csrf = null): void
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if (null !== $csrf) {
            $headers['HTTP_X-CSRF-Token'] = $csrf;
        }
        $client->request('POST', $url, [], [], $headers, json_encode($body));
    }

    private function login(KernelBrowser $client, string $email = 'agent1@propertymaster.test', string $password = 'password'): void
    {
        $client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'password' => $password,
        ]));
        self::assertResponseIsSuccessful();
    }

    public function testEnumsArePublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/enums');
        self::assertResponseIsSuccessful();
        $data = $this->json($client);
        self::assertArrayHasKey('propertyCategory', $data);
        self::assertSame('villa', $data['propertyCategory'][0]['value']);
    }

    public function testPropertyListIsPublicAndPaginated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/properties?perPage=5');
        self::assertResponseIsSuccessful();
        $data = $this->json($client);
        self::assertArrayHasKey('data', $data);
        self::assertArrayHasKey('meta', $data);
        self::assertLessThanOrEqual(5, count($data['data']));
        self::assertGreaterThan(0, $data['meta']['total']);
        // Public list must only expose published properties.
        foreach ($data['data'] as $p) {
            self::assertArrayHasKey('priceFormatted', $p);
            self::assertArrayHasKey('coverImageUrl', $p);
        }
    }

    public function testMeReturnsNullWhenAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me');
        self::assertResponseIsSuccessful();
        self::assertNull($this->json($client)['user']);
    }

    public function testLoginThenMeReturnsUser(): void
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/api/me');
        self::assertResponseIsSuccessful();
        $user = $this->json($client)['user'];
        self::assertSame('agent1@propertymaster.test', $user['email']);
        self::assertContains('ROLE_AGENT', $user['roles']);
    }

    public function testInvalidLoginReturns401(): void
    {
        $client = static::createClient();
        $this->post($client, '/api/auth/login', ['email' => 'agent1@propertymaster.test', 'password' => 'wrong']);
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertSame('invalid_credentials', $this->json($client)['error']['code']);
    }

    public function testCsrfRequiredForMutations(): void
    {
        $client = static::createClient();
        $this->login($client);
        // No CSRF header on a favourite POST -> 403.
        $listing = $this->firstPublishedProperty($client);
        $this->post($client, '/api/favourites/'.$listing['id'], []);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertSame('csrf_invalid', $this->json($client)['error']['code']);
    }

    public function testFavouriteToggle(): void
    {
        $client = static::createClient();
        $this->login($client, 'user1@propertymaster.test');
        $csrf = $this->csrf($client);
        $listing = $this->firstPublishedProperty($client);

        $this->post($client, '/api/favourites/'.$listing['id'], [], $csrf);
        self::assertResponseIsSuccessful();
        self::assertTrue($this->json($client)['favourited']);

        $client->request('GET', '/api/favourites');
        self::assertResponseIsSuccessful();
        $ids = array_column($this->json($client)['data'], 'id');
        self::assertContains($listing['id'], $ids);

        $client->request('DELETE', '/api/favourites/'.$listing['id'], [], [], ['HTTP_X-CSRF-Token' => $csrf]);
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testAgentCanCreatePropertyAndOwnershipIsEnforced(): void
    {
        $client = static::createClient();
        $this->login($client, 'agent1@propertymaster.test');
        $csrf = $this->csrf($client);

        $this->post($client, '/api/properties', [
            'title' => 'Test Villa',
            'description' => 'A lovely test villa with a garden.',
            'listingType' => 'sale',
            'category' => 'villa',
            'type' => 'residential',
            'status' => 'published',
            'price' => 5000000,
            'area' => 2400,
            'bedRooms' => 3,
            'bathRooms' => 2,
            'city' => 'Mohali',
        ], $csrf);
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $created = $this->json($client);
        self::assertSame('Test Villa', $created['title']);
        self::assertSame(500000000, $created['price']); // minor units

        // A different agent cannot edit it (re-login on the same client).
        $this->login($client, 'agent2@propertymaster.test');
        $otherCsrf = $this->csrf($client);
        $this->requestJson($client, 'PATCH', '/api/properties/'.$created['id'], ['title' => 'Hijacked'], $otherCsrf);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreatePropertyValidationErrors(): void
    {
        $client = static::createClient();
        $this->login($client, 'agent1@propertymaster.test');
        $csrf = $this->csrf($client);
        $this->post($client, '/api/properties', ['title' => '', 'category' => 'not_a_category'], $csrf);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $error = $this->json($client)['error'];
        self::assertSame('validation_failed', $error['code']);
        self::assertNotEmpty($error['violations']);
    }

    public function testAnonymousCannotCreateProperty(): void
    {
        $client = static::createClient();
        $csrf = $this->csrf($client);
        $this->post($client, '/api/properties', ['title' => 'x'], $csrf);
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    private function requestJson(KernelBrowser $client, string $method, string $url, array $body, string $csrf): void
    {
        $client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-CSRF-Token' => $csrf,
        ], json_encode($body));
    }

    private function firstPublishedProperty(KernelBrowser $client): array
    {
        $client->request('GET', '/api/properties?perPage=1');
        return $this->json($client)['data'][0];
    }
}
