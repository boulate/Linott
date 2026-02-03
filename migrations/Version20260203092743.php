<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203092743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create jour_type and jour_type_periode tables for template day feature';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jour_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, partage TINYINT NOT NULL, actif TINYINT NOT NULL, ordre INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_C9EC2EEAA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE jour_type_periode (id INT AUTO_INCREMENT NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, ordre INT NOT NULL, jour_type_id INT NOT NULL, section_id INT NOT NULL, axe1_id INT DEFAULT NULL, axe2_id INT DEFAULT NULL, axe3_id INT DEFAULT NULL, INDEX IDX_EF9AE6B5608F37F0 (jour_type_id), INDEX IDX_EF9AE6B5D823E37A (section_id), INDEX IDX_EF9AE6B5D7996C58 (axe1_id), INDEX IDX_EF9AE6B5C52CC3B6 (axe2_id), INDEX IDX_EF9AE6B57D90A4D3 (axe3_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE jour_type ADD CONSTRAINT FK_C9EC2EEAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE jour_type_periode ADD CONSTRAINT FK_EF9AE6B5608F37F0 FOREIGN KEY (jour_type_id) REFERENCES jour_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jour_type_periode ADD CONSTRAINT FK_EF9AE6B5D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE jour_type_periode ADD CONSTRAINT FK_EF9AE6B5D7996C58 FOREIGN KEY (axe1_id) REFERENCES axe1 (id)');
        $this->addSql('ALTER TABLE jour_type_periode ADD CONSTRAINT FK_EF9AE6B5C52CC3B6 FOREIGN KEY (axe2_id) REFERENCES axe2 (id)');
        $this->addSql('ALTER TABLE jour_type_periode ADD CONSTRAINT FK_EF9AE6B57D90A4D3 FOREIGN KEY (axe3_id) REFERENCES axe3 (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jour_type DROP FOREIGN KEY FK_C9EC2EEAA76ED395');
        $this->addSql('ALTER TABLE jour_type_periode DROP FOREIGN KEY FK_EF9AE6B5608F37F0');
        $this->addSql('ALTER TABLE jour_type_periode DROP FOREIGN KEY FK_EF9AE6B5D823E37A');
        $this->addSql('ALTER TABLE jour_type_periode DROP FOREIGN KEY FK_EF9AE6B5D7996C58');
        $this->addSql('ALTER TABLE jour_type_periode DROP FOREIGN KEY FK_EF9AE6B5C52CC3B6');
        $this->addSql('ALTER TABLE jour_type_periode DROP FOREIGN KEY FK_EF9AE6B57D90A4D3');
        $this->addSql('DROP TABLE jour_type');
        $this->addSql('DROP TABLE jour_type_periode');
    }
}
