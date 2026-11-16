<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261116131753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Retire le commentaire DC2Type de species.habitats, plus utilisé depuis DBAL 4';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE species CHANGE habitats habitats LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE species CHANGE habitats habitats LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
