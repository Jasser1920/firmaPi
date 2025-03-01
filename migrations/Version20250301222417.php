<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301222417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP INDEX UNIQ_6EEAA67D8E54FB25, ADD INDEX IDX_6EEAA67D8E54FB25 (livraison_id)');
        $this->addSql('ALTER TABLE commande CHANGE livraison_id livraison_id INT NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL COMMENT \'(DC2Type:statut_commande)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP INDEX IDX_6EEAA67D8E54FB25, ADD UNIQUE INDEX UNIQ_6EEAA67D8E54FB25 (livraison_id)');
        $this->addSql('ALTER TABLE commande CHANGE livraison_id livraison_id INT DEFAULT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
    }
}
