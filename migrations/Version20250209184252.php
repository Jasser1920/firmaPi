<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209184252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agriculteur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, mdp VARCHAR(100) NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, nom_association VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dons (id INT AUTO_INCREMENT NOT NULL, produits_id INT DEFAULT NULL, donateur_id INT NOT NULL, association_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantite INT NOT NULL, INDEX IDX_E4F955FACD11A2CF (produits_id), INDEX IDX_E4F955FAA9C80E3 (donateur_id), INDEX IDX_E4F955FAEFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, association_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', lieu VARCHAR(255) NOT NULL, desription LONGTEXT DEFAULT NULL, INDEX IDX_B26681EEFB9C8A5 (association_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, agriculteur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, quantite INT NOT NULL, unite VARCHAR(255) NOT NULL, prix NUMERIC(10, 0) NOT NULL, date_exp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_29A5EC277EBB810E (agriculteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FACD11A2CF FOREIGN KEY (produits_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FAA9C80E3 FOREIGN KEY (donateur_id) REFERENCES agriculteur (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FAEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC277EBB810E FOREIGN KEY (agriculteur_id) REFERENCES agriculteur (id)');
        $this->addSql('DROP TABLE user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_verified TINYINT(1) NOT NULL, full_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FACD11A2CF');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FAA9C80E3');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FAEFB9C8A5');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EEFB9C8A5');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC277EBB810E');
        $this->addSql('DROP TABLE agriculteur');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE dons');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE produit');
    }
}
