<?php

declare(strict_types=1);

namespace App\Controller\Default;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '', name: 'app_default_index')]
class Index extends AbstractController
{
    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return $this->redirectToRoute('app_authors_list');
    }
}
