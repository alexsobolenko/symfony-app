<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250615071728 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '[messenger_messages]: table created';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $sql = <<<SQL
            CREATE TABLE `messenger_messages` (
                `id` BIGINT AUTO_INCREMENT NOT NULL,
                `body` LONGTEXT NOT NULL,
                `headers` LONGTEXT NOT NULL,
                `queue_name` VARCHAR(190) NOT NULL,
                `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `available_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `delivered_at` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_75EA56E0FB7336F0 (`queue_name`),
                INDEX IDX_75EA56E0E3BD61CE (`available_at`),
                INDEX IDX_75EA56E016BA31DB (`delivered_at`),
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
        $this->addSql('DROP TABLE `messenger_messages`');
    }
}
