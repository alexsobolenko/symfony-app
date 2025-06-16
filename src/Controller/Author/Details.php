<?php

declare(strict_types=1);

namespace App\Controller\Author;

use App\Form\AuthorType;
use App\Model\DTO\AuthorModel;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/authors/create', name: 'app_author_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
#[Route(path: '/authors/edit/{id}', name: 'app_author_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class Details extends AbstractController
{
    /**
     * @param Request $request
     * @param AuthorRepository $repository
     * @param TranslatorInterface $translator
     * @param int|null $id
     * @return Response
     */
    public function __invoke(
        Request $request,
        AuthorRepository $repository,
        TranslatorInterface $translator,
        ?int $id = null
    ): Response {
        try {
            if ($id === null) {
                $model = new AuthorModel();
                $title = $translator->trans('title.authors.create');
            } else {
                $author = $repository->get($id);
                $model = AuthorModel::map($author);
                $title = $translator->trans('title.authors.edit', ['%name%' => $author->getName()]);
            }

            $form = $this->createForm(AuthorType::class, $model);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                $author = $id === null
                    ? $repository->create($model->name)
                    : $repository->edit($id, $model->name);
                $this->addFlash('success', $translator->trans('message.author.saved', [
                    '%name%' => $author->getName(),
                ]));

                return $this->redirectToRoute('app_authors_list');
            }
        } catch (\Throwable $e) {
            $this->addFlash('danger', $translator->trans($e->getMessage()));

            return $this->redirectToRoute('app_authors_list');
        }

        return $this->render('author/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }
}
