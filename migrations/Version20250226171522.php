<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226171522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE date_commande date_commande DATE NOT NULL');
        $this->addSql('ALTER TABLE terrain ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP localisation');
        $this->addSql('ALTER TABLE utilisateur ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD blocked TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE date_commande date_commande DATETIME NOT NULL');
        $this->addSql('ALTER TABLE terrain ADD localisation VARCHAR(255) NOT NULL, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE utilisateur DROP profile_picture, DROP blocked');
    }
}
