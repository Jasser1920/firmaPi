<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216154327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location ADD terrain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB8A2D8B41 FOREIGN KEY (terrain_id) REFERENCES terrain (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB8A2D8B41 ON location (terrain_id)');
        $this->addSql('ALTER TABLE produit ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27FB88E14F ON produit (utilisateur_id)');
        $this->addSql('ALTER TABLE terrain ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_C87653B1FB88E14F ON terrain (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB8A2D8B41');
        $this->addSql('DROP INDEX IDX_5E9E89CB8A2D8B41 ON location');
        $this->addSql('ALTER TABLE location DROP terrain_id');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27FB88E14F');
        $this->addSql('DROP INDEX IDX_29A5EC27FB88E14F ON produit');
        $this->addSql('ALTER TABLE produit DROP utilisateur_id');
        $this->addSql('ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1FB88E14F');
        $this->addSql('DROP INDEX IDX_C87653B1FB88E14F ON terrain');
        $this->addSql('ALTER TABLE terrain DROP utilisateur_id');
    }
}
