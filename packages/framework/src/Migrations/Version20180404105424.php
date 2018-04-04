<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180404105424 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE order_items ADD ext_id INT DEFAULT NULL');
        $this->sql('ALTER TABLE order_items ALTER name DROP NOT NULL');
        $this->sql('ALTER TABLE order_items ALTER price_without_vat DROP NOT NULL');
        $this->sql('ALTER TABLE order_items ALTER price_with_vat DROP NOT NULL');
        $this->sql('ALTER TABLE order_items ALTER vat_percent DROP NOT NULL');
        $this->sql('ALTER TABLE order_items ALTER quantity DROP NOT NULL');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
