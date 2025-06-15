<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Form\BookType;
use App\Model\DTO\BookModel;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/books/create', name: 'app_book_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
#[Route(path: '/books/edit/{id}', name: 'app_book_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class Details extends AbstractController
{
    /**
     * @param Request $request
     * @param BookRepository $repository
     * @param TranslatorInterface $translator
     * @param int|null $id
     * @return Response
     */
    public function __invoke(
        Request $request,
        BookRepository $repository,
        TranslatorInterface $translator,
        ?int $id = null
    ): Response {
        try {
            if ($id === null) {
                $model = new BookModel();
                $title = $translator->trans('title.books.create');
            } else {
                $book = $repository->get($id);
                $model = BookModel::map($book);
                $title = $translator->trans('title.books.edit', [
                    '%name%' => $book->getName(),
                    '%author%' => $book->getAuthor()->getName(),
                ]);
            }

            $form = $this->createForm(BookType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                $book = $id === null
                    ? $repository->create($model->author, $model->name, $model->price)
                    : $repository->edit($id, $model->author, $model->name, $model->price);
                $this->addFlash('success', $translator->trans('message.book.saved', ['%name%' => $book->getNameWithAuthor()]));

                return $this->redirectToRoute('app_books_list');
            }
        } catch (\Throwable $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));

            return $this->redirectToRoute('app_books_list');
        }

        return $this->render('book/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }
}
