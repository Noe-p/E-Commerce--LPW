<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220306225113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD address_id INT DEFAULT NULL, ADD order_id_id INT DEFAULT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD phone VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES customer_address (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F5B7AF75 ON user (address_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649FCDAEAAA ON user (order_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE customer_address CHANGE last_name last_name VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE firs_name firs_name VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE city city VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image image VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FCDAEAAA');
        $this->addSql('DROP INDEX IDX_8D93D649F5B7AF75 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649FCDAEAAA ON user');
        $this->addSql('ALTER TABLE user DROP address_id, DROP order_id_id, DROP last_name, DROP first_name, DROP phone, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
