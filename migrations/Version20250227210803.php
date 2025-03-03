<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227210803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Modifie date_creation pour être nullable avec DEFAULT CURRENT_TIMESTAMP
        $this->addSql('ALTER TABLE terrain MODIFY date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NULL');
    }

    public function down(Schema $schema): void
    {
        // Revertit à l’état précédent (non nullable, type DATE)
        $this->addSql('ALTER TABLE terrain MODIFY date_creation DATE NOT NULL');
    }
}