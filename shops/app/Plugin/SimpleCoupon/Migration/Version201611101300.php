<?php
/*
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201611101300 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createConditionCustomer($schema);
		$this->updateCoupon($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_simple_coupon_condition_customer');
        $schema->dropSequence('plg_simple_coupon_condition_customer_id_seq');
    }

    /**
     * クーポン情報テーブル作成
     * create sequence plg_simple_coupon_condition_customer_id_seq
     * CREATE TABLE `plg_simple_coupon_condition_customer` (
  		`config_id` integer NOT NULL PRIMARY KEY,
  		`coupon_payment_id` integer DEFAULT NULL,
		  `create_date` timestamp(0) without time zone NOT NULL,
		  `update_date` timestamp(0) without time zone NOT NULL,
		) 
     * @param Schema $schema
     */
    protected function createConditionCustomer(Schema $schema)
    {
    	$table = $schema->createTable("plg_simple_coupon_condition_customer");
    	$table->addColumn('condition_id', 'bigint', array(
    			'autoincrement' => true,
    			'notnull' => true,
    			'unsigned' => true,
    			'length' => 20,
    	));
    	$table->addColumn('coupon_id', 'bigint', array(
    			'notnull' => false,
    			'unsigned' => true,
    			'length' => 20,
    	));
    	$table->addColumn('customer_id', 'integer', array(
    			'notnull' => false,
    	));
    	$table->setPrimaryKey(array('condition_id'));
    }
    
    protected function updateCoupon(Schema $schema)
    {
    	$table = $schema->getTable("plg_simple_coupon_coupon");
    	$table->addColumn('condition_type', 'smallint', array(
    			'notnull' => true,
        		'unsigned' => false,
        		'default' => 0,
    	));
    	
    }
}