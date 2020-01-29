<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110143609 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ledger ADD user_id INT DEFAULT NULL, DROP bongo_id');
        $this->addSql('ALTER TABLE ledger ADD CONSTRAINT FK_C07BA4BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C07BA4BCA76ED395 ON ledger (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ledger DROP FOREIGN KEY FK_C07BA4BCA76ED395');
        $this->addSql('DROP INDEX IDX_C07BA4BCA76ED395 ON ledger');
        $this->addSql('ALTER TABLE ledger ADD bongo_id INT NOT NULL, DROP user_id');
    }
}
