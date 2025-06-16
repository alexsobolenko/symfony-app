<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param int $id
     * @return Author
     * @throws NotFoundHttpException
     */
    public function get(int $id): Author
    {
        $author = $this->find($id);
        if (!$author instanceof Author) {
            throw new NotFoundHttpException('error.author_not_found');
        }

        return $author;
    }

    /**
     * @param string $name
     * @return Author
     * @throws BadRequestHttpException
     */
    public function create(string $name): Author
    {
        try {
            $author = new Author($name);
            $this->getEntityManager()->persist($author);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $author;
    }

    /**
     * @param int $id
     * @param string $name
     * @return Author
     * @throws BadRequestHttpException
     */
    public function edit(int $id, string $name): Author
    {
        $author = $this->get($id);
        try {
            $author->setName($name);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $author;
    }

    /**
     * @param int $id
     * @return Author
     * @throws BadRequestHttpException
     */
    public function delete(int $id): Author
    {
        $author = $this->get($id);
        try {
            $this->getEntityManager()->remove($author);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $author;
    }

    /**
     * @return bool
     */
    public function hasAuthors(): bool
    {
        $authors = $this->findAll();

        return count($authors) > 0;
    }

    /**
     * @param array $filters
     * @return int
     * @throws BadRequestHttpException
     */
    public function countByFilter(array $filters): int
    {
        try {
            $qb = $this->createQuery($filters);
            $qb->select('COUNT(a.id)');

            return (int)$qb->getQuery()->getSingleScalarResult();
        } catch (ORMException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }

    /**
     * @param array $filters
     * @return array
     * @throws BadRequestHttpException
     */
    public function findByFilter(array $filters): array
    {
        try {
            $page = $filters['page'] ?? 1;
            $limit = $filters['limit'] ?? 1;

            $qb = $this->createQuery($filters);
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($page - 1) * $limit);

            $qb->addOrderBy('a.name', Order::Ascending->value);

            return $qb->getQuery()->getResult();
        } catch (ORMException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function createQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        $name = $filters['name'] ?? null;
        if ($name) {
            $qb->andWhere($qb->expr()->like('a.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        return $qb;
    }
}
