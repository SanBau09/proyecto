<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309150651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE incidencia (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_cliente_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, estado VARCHAR(255) NOT NULL, INDEX IDX_C7C6728C79F37AE5 (id_user_id), INDEX IDX_C7C6728C7BF9CE86 (id_cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728C79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728C7BF9CE86 FOREIGN KEY (id_cliente_id) REFERENCES cliente (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728C79F37AE5');
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728C7BF9CE86');
        $this->addSql('DROP TABLE incidencia');
    }
}
