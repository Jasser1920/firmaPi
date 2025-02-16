<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216153400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD livraison_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D8E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6EEAA67D8E54FB25 ON commande (livraison_id)');
        $this->addSql('ALTER TABLE don ADD evenement_id INT DEFAULT NULL, ADD dons_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenemment (id)');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D923A9D5B4 FOREIGN KEY (dons_user_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_F8F081D9FD02F13 ON don (evenement_id)');
        $this->addSql('CREATE INDEX IDX_F8F081D923A9D5B4 ON don (dons_user_id)');
        $this->addSql('ALTER TABLE evenemment ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenemment ADD CONSTRAINT FK_6A27C76FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_6A27C76FB88E14F ON evenemment (utilisateur_id)');
        $this->addSql('ALTER TABLE reclammation ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclammation ADD CONSTRAINT FK_1F8C1D97FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_1F8C1D97FB88E14F ON reclammation (utilisateur_id)');
        $this->addSql('ALTER TABLE reponse_reclamation ADD reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse_reclamation ADD CONSTRAINT FK_C7CB51012D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclammation (id)');
        $this->addSql('CREATE INDEX IDX_C7CB51012D6BA2D9 ON reponse_reclamation (reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D8E54FB25');
        $this->addSql('DROP INDEX UNIQ_6EEAA67D8E54FB25 ON commande');
        $this->addSql('ALTER TABLE commande DROP livraison_id');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9FD02F13');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D923A9D5B4');
        $this->addSql('DROP INDEX IDX_F8F081D9FD02F13 ON don');
        $this->addSql('DROP INDEX IDX_F8F081D923A9D5B4 ON don');
        $this->addSql('ALTER TABLE don DROP evenement_id, DROP dons_user_id');
        $this->addSql('ALTER TABLE evenemment DROP FOREIGN KEY FK_6A27C76FB88E14F');
        $this->addSql('DROP INDEX IDX_6A27C76FB88E14F ON evenemment');
        $this->addSql('ALTER TABLE evenemment DROP utilisateur_id');
        $this->addSql('ALTER TABLE reclammation DROP FOREIGN KEY FK_1F8C1D97FB88E14F');
        $this->addSql('DROP INDEX IDX_1F8C1D97FB88E14F ON reclammation');
        $this->addSql('ALTER TABLE reclammation DROP utilisateur_id');
        $this->addSql('ALTER TABLE reponse_reclamation DROP FOREIGN KEY FK_C7CB51012D6BA2D9');
        $this->addSql('DROP INDEX IDX_C7CB51012D6BA2D9 ON reponse_reclamation');
        $this->addSql('ALTER TABLE reponse_reclamation DROP reclamation_id');
    }
}
