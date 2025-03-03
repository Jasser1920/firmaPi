<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302223602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, evenement_id INT NOT NULL, participation_date DATETIME NOT NULL, INDEX IDX_AB55E24FA76ED395 (user_id), INDEX IDX_AB55E24FFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenemment (id)');
        $this->addSql('ALTER TABLE commande DROP INDEX UNIQ_6EEAA67D8E54FB25, ADD INDEX IDX_6EEAA67D8E54FB25 (livraison_id)');
        $this->addSql('ALTER TABLE commande CHANGE livraison_id livraison_id INT NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL COMMENT \'(DC2Type:statut_commande)\'');
        $this->addSql('ALTER TABLE don CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE evenemment ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('DROP TABLE participation');
        $this->addSql('ALTER TABLE commande DROP INDEX IDX_6EEAA67D8E54FB25, ADD UNIQUE INDEX UNIQ_6EEAA67D8E54FB25 (livraison_id)');
        $this->addSql('ALTER TABLE commande CHANGE livraison_id livraison_id INT DEFAULT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE don CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenemment DROP image');
    }
}
