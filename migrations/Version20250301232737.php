<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301232737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP type_livraison, DROP nom, DROP prenom, DROP mail, CHANGE date_commande date_commande DATE NOT NULL, CHANGE total total DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE livraison DROP num_tel, CHANGE nom_societe nom_societe VARCHAR(255) NOT NULL, CHANGE date_livraison date_livraison DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD type_livraison VARCHAR(255) DEFAULT NULL, ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD mail VARCHAR(255) NOT NULL, CHANGE date_commande date_commande DATE DEFAULT NULL, CHANGE total total DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE livraison ADD num_tel VARCHAR(255) NOT NULL, CHANGE nom_societe nom_societe VARCHAR(255) DEFAULT NULL, CHANGE date_livraison date_livraison DATE DEFAULT NULL');
    }
}
