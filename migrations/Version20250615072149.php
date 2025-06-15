<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250615072149 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '[authors]: table created';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $sql = <<<SQL
            CREATE TABLE `authors` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                PRIMARY KEY(`id`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL;
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `authors`');
    }
}
