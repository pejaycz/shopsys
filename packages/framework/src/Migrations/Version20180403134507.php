<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180403134507 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE products_users (
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                PRIMARY KEY(product_id, user_id)
            )');
        $this->sql('CREATE INDEX IDX_F4DA5B394584665A ON products_users (product_id)');
        $this->sql('CREATE INDEX IDX_F4DA5B39A76ED395 ON products_users (user_id)');
        $this->sql('
            ALTER TABLE
                products_users
            ADD
                CONSTRAINT FK_F4DA5B394584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('
            ALTER TABLE
                products_users
            ADD
                CONSTRAINT FK_F4DA5B39A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
