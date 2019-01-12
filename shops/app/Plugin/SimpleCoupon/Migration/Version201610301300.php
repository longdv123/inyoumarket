<?php
/*
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201610301300 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createConfig($schema);
        $this->createCoupon($schema);
        $this->createCouponOrder($schema);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_simple_coupon_config');
        $schema->dropSequence('plg_simple_coupon_config_id_seq');
        $schema->dropTable('plg_simple_coupon_coupon');
        $schema->dropSequence('plg_simple_coupon_coupon_id_seq');
        $schema->dropTable('plg_simple_coupon_coupon_order');
        $schema->dropSequence('plg_simple_coupon_coupon_order_id_seq');
    }

    /**
     * クーポン情報テーブル作成
     * create sequence plg_simple_coupon_config_id_seq
     * CREATE TABLE `plg_simple_coupon_config` (
  		`config_id` integer NOT NULL PRIMARY KEY,
  		`coupon_payment_id` integer DEFAULT NULL,
		  `create_date` timestamp(0) without time zone NOT NULL,
		  `update_date` timestamp(0) without time zone NOT NULL,
		) 
     * @param Schema $schema
     */
    protected function createConfig(Schema $schema)
    {
    	$table = $schema->createTable("plg_simple_coupon_config");
    	$table->addColumn('config_id', 'integer', array(
    			'autoincrement' => true,
    			'notnull' => true,
    	));
    
    	$table->addColumn('coupon_payment_id', 'integer', array(
    			'notnull' => false,
    	));
    
    	$table->addColumn('create_date', 'datetime', array(
    			'notnull' => false,
    			'unsigned' => false,
    	));
    
    	$table->addColumn('update_date', 'datetime', array(
    			'notnull' => false,
    			'unsigned' => false,
    	));
    
    	$table->setPrimaryKey(array('config_id'));
    }
    
    /**
     * クーポン情報テーブル作成
     * create sequence plg_simple_coupon_coupon_id_seq
     * CREATE TABLE `plg_simple_coupon_coupon` (
		  `coupon_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `coupon_name` varchar(200) NOT NULL,
		  `coupon_code` varchar(50) NOT NULL,
		  `from_date` without time zone NOT NULL,
		  `to_date` without time zone DEFAULT NULL,
		  `discount_value` int(11) NOT NULL DEFAULT '0',
		  `discount_type` int(11) NOT NULL,
		  `combined_use_flg` smallint(6) NOT NULL DEFAULT '0',
		  `guest_use_flg` smallint(6) NOT NULL DEFAULT '0',
		  `number_of_issued` int(11) NOT NULL DEFAULT '0',
		  `status` smallint(6) NOT NULL,
		  `del_flg` smallint(6) NOT NULL DEFAULT '0',
		  `create_date`  without time zone DEFAULT NULL,
		  `update_date`  without time zone DEFAULT NULL,
		) 
     * @param Schema $schema
     */
    protected function createCoupon(Schema $schema)
    {
        $table = $schema->createTable("plg_simple_coupon_coupon");
        $table->addColumn('coupon_id', 'bigint', array(
            'autoincrement' => true,
            'notnull' => true,
        	'unsigned' => true,
        	'length' => 20,
        ));

        $table->addColumn('coupon_name', 'string', array(
        	'notnull' => true,
        	'length' => 200,
        ));
        
        $table->addColumn('coupon_code', 'text', array(
            'notnull' => true,
        	'length' => 50,
        ));

        $table->addColumn('from_date', 'datetime', array(
        		'notnull' => true,
        		'unsigned' => false,
        ));
        
        $table->addColumn('to_date', 'datetime', array(
        		'notnull' => false,
        		'unsigned' => false,
        ));

        $table->addColumn('discount_value', 'integer', array(
            'notnull' => true,
        	'default' => 0,
        ));

        $table->addColumn('discount_type', 'integer', array(
            'notnull' => true,
        ));

        $table->addColumn('combined_use_flg', 'smallint', array(
        		'notnull' => true,
        		'unsigned' => false,
        		'default' => 0,
        ));
        
        $table->addColumn('guest_use_flg', 'smallint', array(
        		'notnull' => true,
        		'unsigned' => false,
        		'default' => 0,
        ));
        
        $table->addColumn('number_of_issued', 'integer', array(
        		'notnull' => true,
        		'default' => 0,
        ));
        
        $table->addColumn('status', 'smallint', array(
        		'notnull' => true,
        		'unsigned' => false,
        ));
        
        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => false,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => false,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('coupon_id'));
    }

    /**
     * 注文クーポン情報テーブルの作成
     * CREATE TABLE `plg_simple_coupon_coupon_order` (
		  `coupon_order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `coupon_id` bigint(20) unsigned NOT NULL,
		  `order_id` int(11) DEFAULT NULL,
		  `customer_id` int(11) DEFAULT NULL,
		  `email` longtext,
		  `status` smallint(6) DEFAULT NULL ,
		  `discount_price` int(11) NOT NULL DEFAULT '0',
		  `create_date` datetime DEFAULT NULL,
		) 
     * @param Schema $schema
     */
    protected function createCouponOrder(Schema $schema) {
        $table = $schema->createTable("plg_simple_coupon_coupon_order");
        $table->addColumn('coupon_order_id', 'bigint', array(
            'autoincrement' => true,
            'notnull' => true,
        	'unsigned' => true,
        	'length' => 20,
        ));

        $table->addColumn('coupon_id', 'bigint', array(
            'notnull' => true,
        	'unsigned' => true,
        	'length' => 20,
        ));

        $table->addColumn('order_id', 'integer', array(
            'notnull' => false,
        ));
        
        $table->addColumn('customer_id', 'integer', array(
        	'notnull' => false,
        ));
        
        $table->addColumn('email', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('status', 'smallint', array(
        	'notnull' => true,
        	'unsigned' => false,
        ));
        
        $table->addColumn('discount_price', 'integer', array(
        	'notnull' => true,
        	'default' => 0,
        ));
        
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => false,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('coupon_order_id'));
    }
}