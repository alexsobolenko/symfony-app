<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250615072235 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '[books]: table created';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $sql = <<<SQL
            CREATE TABLE `books` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `author_id` INT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `price` DOUBLE PRECISION NOT NULL,
                INDEX IDX_4A1B2A92F675F31B (`author_id`),
                PRIMARY KEY(`id`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL;
        $this->addSql($sql);

        $sql = <<<SQL
            ALTER TABLE `books` ADD CONSTRAINT FK_4A1B2A92F675F31B
                FOREIGN KEY (`author_id`)
                REFERENCES `authors` (`id`)
        SQL;
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `books` DROP FOREIGN KEY FK_4A1B2A92F675F31B');
        $this->addSql('DROP TABLE `books`');
    }
}
