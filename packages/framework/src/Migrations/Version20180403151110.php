<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180403151110 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('CREATE TABLE codes (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->sql('ALTER TABLE orders ADD code_id INT DEFAULT NULL');
        $this->sql('
            ALTER TABLE
                orders
            ADD
                CONSTRAINT FK_E52FFDEE27DAFE17 FOREIGN KEY (code_id) REFERENCES codes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('CREATE UNIQUE INDEX UNIQ_E52FFDEE27DAFE17 ON orders (code_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
