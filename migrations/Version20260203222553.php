<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create default "_IND_" parent elements for orphan axes when using independent mode
 */
final class Version20260203222553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Inserts default _IND_ parent elements for orphan axes in independent mode';
    }

    public function up(Schema $schema): void
    {
        // Insert default Section for orphans (actif=false so it won't appear in normal lists)
        $this->addSql("INSERT INTO section (code, libelle, actif, ordre, couleur) VALUES ('_IND_', 'Indépendants (système)', 0, 9999, 'gray')");

        // Get the section ID and insert default Axe1
        $this->addSql("INSERT INTO axe1 (code, libelle, section_id, actif, ordre) SELECT '_IND_', 'Indépendant (système)', id, 0, 9999 FROM section WHERE code = '_IND_'");

        // Get the axe1 ID and insert default Axe2
        $this->addSql("INSERT INTO axe2 (code, libelle, axe1_id, actif, ordre) SELECT '_IND_', 'Indépendant (système)', id, 0, 9999 FROM axe1 WHERE code = '_IND_'");
    }

    public function down(Schema $schema): void
    {
        // Delete in reverse order due to foreign key constraints
        $this->addSql("DELETE FROM axe2 WHERE code = '_IND_'");
        $this->addSql("DELETE FROM axe1 WHERE code = '_IND_'");
        $this->addSql("DELETE FROM section WHERE code = '_IND_'");
    }
}
