<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param int $id
     * @return Book
     * @throws NotFoundHttpException
     */
    public function get(int $id): Book
    {
        $book = $this->find($id);
        if (!$book instanceof Book) {
            throw new NotFoundHttpException('error.book_not_found');
        }

        return $book;
    }

    /**
     * @param int $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws BadRequestHttpException
     */
    public function create(int $authorId, string $name, float $price): Book
    {
        $author = $this->getEntityManager()->getRepository(Author::class)->get($authorId);
        try {
            $book = new Book($author, $name, $price);
            $this->getEntityManager()->persist($book);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $book;
    }

    /**
     * @param int $id
     * @param int $authorId
     * @param string $name
     * @param float $price
     * @return Book
     * @throws BadRequestHttpException
     */
    public function edit(int $id, int $authorId, string $name, float $price): Book
    {
        $author = $this->getEntityManager()->getRepository(Author::class)->get($authorId);
        $book = $this->get($id);
        try {
            $book->setAuthor($author);
            $book->setName($name);
            $book->setPrice($price);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $book;
    }

    /**
     * @param int $id
     * @return Book
     * @throws BadRequestHttpException
     */
    public function delete(int $id): Book
    {
        $book = $this->get($id);
        try {
            $this->getEntityManager()->remove($book);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $book;
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
            $qb->select('COUNT(b.id)');

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

            $qb->addOrderBy('b.price', Order::Ascending->value);
            $qb->addOrderBy('b.name', Order::Ascending->value);

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
        $qb = $this->createQueryBuilder('b');

        $name = $filters['name'] ?? null;
        if ($name) {
            $qb->andWhere($qb->expr()->like('b.name', ':name'));
            $qb->leftJoin('b.author', 'a');
            $qb->andWhere($qb->expr()->like('a.name', ':name'));
            $qb->setParameter('name', '%' . $name . '%');
        }

        return $qb;
    }
}
