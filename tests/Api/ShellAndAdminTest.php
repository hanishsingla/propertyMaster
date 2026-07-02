<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ShellAndAdminTest extends WebTestCase
{
    public function testSpaShellIsServedForRootAndClientRoutes(): void
    {
        $client = static::createClient();

        foreach (['/', '/properties', '/login', '/some/deep/client/route'] as $path) {
            $client->request('GET', $path);
            self::assertResponseIsSuccessful("shell should render for $path");
            self::assertStringContainsString('id="root"', $client->getResponse()->getContent());
            self::assertStringContainsString('csrf-token', $client->getResponse()->getContent());
        }
    }

    public function testUnknownApiRouteIsNotSwallowedBySpa(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/does-not-exist');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testAdminRequiresAdminRole(): void
    {
        $client = static::createClient();

        // Anonymous -> redirected to /login (entry point).
        $client->request('GET', '/admin');
        self::assertResponseRedirects('/login');

        // Regular user -> 403.
        $this->loginJson($client, 'user1@propertymaster.test');
        $client->request('GET', '/admin');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminCanOpenDashboardAndPropertyList(): void
    {
        $client = static::createClient();
        $this->loginJson($client, 'admin@propertymaster.test');

        $client->request('GET', '/admin');
        self::assertResponseIsSuccessful();

        // EasyAdmin property CRUD index.
        $client->request('GET', '/admin?crudAction=index&crudControllerFqcn='.urlencode(\App\Controller\Admin\PropertyCrudController::class));
        self::assertResponseIsSuccessful();
    }

    private function loginJson($client, string $email, string $password = 'password'): void
    {
        $client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'password' => $password,
        ]));
        self::assertResponseIsSuccessful();
    }
}
