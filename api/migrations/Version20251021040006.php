<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021040006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add initial product data';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO product (id, title, price, currency) VALUES (1, 'Fallout', '1.99', 'USD')");
        $this->addSql("INSERT INTO product (id, title, price, currency) VALUES (2, 'Don''t Starve', '2.99', 'USD')");
        $this->addSql("INSERT INTO product (id, title, price, currency) VALUES (3, 'Baldur''s Gate', '3.99', 'USD')");
        $this->addSql("INSERT INTO product (id, title, price, currency) VALUES (4, 'Icewind Dale', '4.99', 'USD')");
        $this->addSql("INSERT INTO product (id, title, price, currency) VALUES (5, 'Bloodborne', '5.99', 'USD')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM product WHERE id IN (1, 2, 3, 4, 5)");
    }
}
