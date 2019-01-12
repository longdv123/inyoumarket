<?php
/*
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201612131600 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
		$this->updateCoupon($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
    
    protected function updateCoupon(Schema $schema)
    {
    	$table = $schema->getTable("plg_simple_coupon_coupon");
    	$table->addColumn('onetime_use_flg', 'smallint', array(
    			'notnull' => true,
        		'unsigned' => false,
        		'default' => 1,
    	));
    	
    }
}