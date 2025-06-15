<?php

declare(strict_types=1);

namespace App\Controller\Default;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/locale/{locale}', name: 'app_change_locale', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class Locale extends AbstractController
{
    /**
     * @param Request $request
     * @param string $locale
     * @return Response
     */
    public function __invoke(Request $request, string $locale): Response
    {
        $request->getSession()->set('_locale', $locale);
        $previousUrl = $request->server->get('HTTP_REFERER');

        return $this->redirect($previousUrl);
    }
}
