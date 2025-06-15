<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Model\PaginatedDataModel;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/authors', name: 'app_authors_list', methods: Request::METHOD_GET)]
class Index extends AbstractController
{
    /**
     * @param Request $request
     * @param AuthorRepository $repository
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function __invoke(Request $request, AuthorRepository $repository, TranslatorInterface $translator): Response
    {
        $filters = $request->query->all();
        isset($filters['page']) || ($filters['page'] = 1);
        isset($filters['limit']) || ($filters['limit'] = (int) $this->getParameter('authors_on_page'));

        try {
            $total = $repository->countByFilter($filters);
            $items = $repository->findByFilter($filters);
        } catch (\Throwable $e) {
            $total = 0;
            $items = [];
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->render('author/list.html.twig', [
            'title' => $translator->trans('title.authors.list'),
            'authors' => new PaginatedDataModel($total, $filters['limit'], $filters['page'], $items),
        ]);
    }
}
