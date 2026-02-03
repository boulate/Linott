<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203030926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE axe1 (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, ordre INT NOT NULL, section_id INT NOT NULL, INDEX IDX_B168E94AD823E37A (section_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE axe2 (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, ordre INT NOT NULL, axe1_id INT NOT NULL, INDEX IDX_2861B8F0D7996C58 (axe1_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE axe3 (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, ordre INT NOT NULL, axe2_id INT NOT NULL, INDEX IDX_5F668866C52CC3B6 (axe2_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, cle VARCHAR(100) NOT NULL, valeur LONGTEXT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A5E2A5D741401D17 (cle), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE conge (id INT AUTO_INCREMENT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, nb_jours NUMERIC(4, 1) NOT NULL, commentaire LONGTEXT DEFAULT NULL, statut VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_2ED89348A76ED395 (user_id), INDEX IDX_2ED89348C54C8C93 (type_id), INDEX idx_conge_user_date (user_id, date_debut), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE periode (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, validee TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_id INT NOT NULL, section_id INT NOT NULL, axe1_id INT DEFAULT NULL, axe2_id INT DEFAULT NULL, axe3_id INT DEFAULT NULL, INDEX IDX_93C32DF3A76ED395 (user_id), INDEX IDX_93C32DF3D823E37A (section_id), INDEX IDX_93C32DF3D7996C58 (axe1_id), INDEX IDX_93C32DF3C52CC3B6 (axe2_id), INDEX IDX_93C32DF37D90A4D3 (axe3_id), INDEX idx_periode_user_date (user_id, date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, ordre INT NOT NULL, UNIQUE INDEX UNIQ_2D737AEF77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE type_conge (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, libelle VARCHAR(100) NOT NULL, decompte TINYINT NOT NULL, couleur VARCHAR(7) NOT NULL, actif TINYINT NOT NULL, UNIQUE INDEX UNIQ_20D414BF77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE axe1 ADD CONSTRAINT FK_B168E94AD823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE axe2 ADD CONSTRAINT FK_2861B8F0D7996C58 FOREIGN KEY (axe1_id) REFERENCES axe1 (id)');
        $this->addSql('ALTER TABLE axe3 ADD CONSTRAINT FK_5F668866C52CC3B6 FOREIGN KEY (axe2_id) REFERENCES axe2 (id)');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED89348A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED89348C54C8C93 FOREIGN KEY (type_id) REFERENCES type_conge (id)');
        $this->addSql('ALTER TABLE periode ADD CONSTRAINT FK_93C32DF3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE periode ADD CONSTRAINT FK_93C32DF3D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE periode ADD CONSTRAINT FK_93C32DF3D7996C58 FOREIGN KEY (axe1_id) REFERENCES axe1 (id)');
        $this->addSql('ALTER TABLE periode ADD CONSTRAINT FK_93C32DF3C52CC3B6 FOREIGN KEY (axe2_id) REFERENCES axe2 (id)');
        $this->addSql('ALTER TABLE periode ADD CONSTRAINT FK_93C32DF37D90A4D3 FOREIGN KEY (axe3_id) REFERENCES axe3 (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE axe1 DROP FOREIGN KEY FK_B168E94AD823E37A');
        $this->addSql('ALTER TABLE axe2 DROP FOREIGN KEY FK_2861B8F0D7996C58');
        $this->addSql('ALTER TABLE axe3 DROP FOREIGN KEY FK_5F668866C52CC3B6');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED89348A76ED395');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED89348C54C8C93');
        $this->addSql('ALTER TABLE periode DROP FOREIGN KEY FK_93C32DF3A76ED395');
        $this->addSql('ALTER TABLE periode DROP FOREIGN KEY FK_93C32DF3D823E37A');
        $this->addSql('ALTER TABLE periode DROP FOREIGN KEY FK_93C32DF3D7996C58');
        $this->addSql('ALTER TABLE periode DROP FOREIGN KEY FK_93C32DF3C52CC3B6');
        $this->addSql('ALTER TABLE periode DROP FOREIGN KEY FK_93C32DF37D90A4D3');
        $this->addSql('DROP TABLE axe1');
        $this->addSql('DROP TABLE axe2');
        $this->addSql('DROP TABLE axe3');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE conge');
        $this->addSql('DROP TABLE periode');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE type_conge');
    }
}
