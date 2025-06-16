<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616085044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_sub_category (product_id INT NOT NULL, sub_category_id INT NOT NULL, INDEX IDX_3147D5F34584665A (product_id), INDEX IDX_3147D5F3F7BFE87C (sub_category_id), PRIMARY KEY(product_id, sub_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_sub_category ADD CONSTRAINT FK_3147D5F34584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_sub_category ADD CONSTRAINT FK_3147D5F3F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_sub_category DROP FOREIGN KEY FK_3147D5F34584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_sub_category DROP FOREIGN KEY FK_3147D5F3F7BFE87C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_sub_category
        SQL);
    }
}
