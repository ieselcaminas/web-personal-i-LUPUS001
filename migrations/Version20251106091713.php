<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106091713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artista (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, nacionalidad VARCHAR(255) DEFAULT NULL, movimiento VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pieza_de_arte (id INT AUTO_INCREMENT NOT NULL, artista_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, anio INT DEFAULT NULL, INDEX IDX_386A9C41AEB0CF13 (artista_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pieza_de_arte ADD CONSTRAINT FK_386A9C41AEB0CF13 FOREIGN KEY (artista_id) REFERENCES artista (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pieza_de_arte DROP FOREIGN KEY FK_386A9C41AEB0CF13');
        $this->addSql('DROP TABLE artista');
        $this->addSql('DROP TABLE pieza_de_arte');
    }
}
