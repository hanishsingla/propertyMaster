<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Serves the React SPA shell for every non-API, non-admin route so client-side
 * routing works. Reserved prefixes (api, admin, profiler, build, uploads, image)
 * are excluded so they resolve to their real handlers or a real 404.
 */
class SpaController extends AbstractController
{
    public function __construct(
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {
    }

    #[Route(
        '/{path}',
        name: 'spa',
        requirements: ['path' => '(?!api(/|$)|admin(/|$)|_(wdt|profiler|error)|build(/|$)|uploads(/|$)|image(/|$)).*'],
        defaults: ['path' => ''],
        methods: ['GET'],
        priority: -100,
    )]
    public function index(CsrfTokenManagerInterface $csrf): Response
    {
        return $this->render('spa/index.html.twig', [
            'vite' => $this->viteAssets(),
            'csrfToken' => $csrf->getToken('api')->getValue(),
        ]);
    }

    /**
     * Resolve the entry assets: use the production manifest when present,
     * otherwise fall back to the Vite dev server.
     *
     * @return array{dev: bool, devServer: string, entry: string, js: string[], css: string[]}
     */
    private function viteAssets(): array
    {
        $manifestPath = $this->projectDir.'/public/build/.vite/manifest.json';
        $entry = 'assets/main.tsx';

        if (is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true) ?: [];
            $chunk = $manifest[$entry] ?? null;
            $js = [];
            $css = [];
            if (null !== $chunk) {
                $js[] = '/build/'.$chunk['file'];
                foreach ($chunk['css'] ?? [] as $file) {
                    $css[] = '/build/'.$file;
                }
            }

            return ['dev' => false, 'devServer' => '', 'entry' => $entry, 'js' => $js, 'css' => $css];
        }

        // Dev fallback: Vite dev server with HMR.
        return [
            'dev' => true,
            'devServer' => 'http://localhost:5173',
            'entry' => '/'.$entry,
            'js' => [],
            'css' => [],
        ];
    }
}
