<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/books/delete/{id}', name: 'app_book_delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class Delete extends AbstractController
{
    /**
     * @param BookRepository $repository
     * @param TranslatorInterface $translator
     * @param int $id
     * @return Response
     */
    public function __invoke(BookRepository $repository, TranslatorInterface $translator, int $id): Response
    {
        try {
            $book = $repository->delete($id);
            $this->addFlash('success', $translator->trans('message.book.deleted', [
                '%name%' => $book->getNameWithAuthor(),
            ]));
        } catch (\Throwable $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('app_books_list');
    }
}
