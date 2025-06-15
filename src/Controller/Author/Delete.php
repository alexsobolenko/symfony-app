<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/authors/delete/{id}', name: 'app_author_delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class Delete extends AbstractController
{
    /**
     * @param AuthorRepository $repository
     * @param TranslatorInterface $translator
     * @param int $id
     * @return Response
     */
    public function __invoke(AuthorRepository $repository, TranslatorInterface $translator, int $id): Response
    {
        try {
            $author = $repository->delete($id);
            $this->addFlash('success', $translator->trans('message.author.deleted', ['%name%' => $author->getName()]));
        } catch (\Throwable $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('app_authors_list');
    }
}
