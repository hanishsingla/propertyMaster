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

    public function testAdminCanOpenDashboardAndReachCrud(): void
    {
        $client = static::createClient();
        $this->loginJson($client, 'admin@propertymaster.test');

        // Dashboard renders (proves EasyAdmin boots on this Symfony version).
        $crawler = $client->request('GET', '/admin');
        self::assertResponseIsSuccessful();

        // The dashboard menu links to the Property and Users CRUDs; follow the
        // Property link (EasyAdmin 5 generates its own signed admin URLs).
        $link = $crawler->filter('a.menu-item-link, .menu a, a')->reduce(
            fn ($node) => str_contains(strtolower($node->text()), 'property')
        )->first();
        self::assertGreaterThan(0, $link->count(), 'dashboard should link to the Property CRUD');

        $client->click($link->link());
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
