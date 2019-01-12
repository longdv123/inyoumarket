<?php
/*
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201709241000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createConditionProduct($schema);
		$this->updateCoupon($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_simple_coupon_condition_product');
        $schema->dropSequence('plg_simple_coupon_condition_product_id_seq');
    }

    /**
     * クーポン情報・商品条件テーブル作成
     * create sequence plg_simple_coupon_condition_product_id_seq
     * @param Schema $schema
     */
    protected function createConditionProduct(Schema $schema)
    {
    	$table = $schema->createTable("plg_simple_coupon_condition_product");
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
    	$table->addColumn('product_id', 'integer', array(
    			'notnull' => false,
        ));
        $table->addColumn('product_class_id', 'integer', array(
                'notnull' => false,
        ));
        $table->addColumn('category_id', 'integer', array(
                'notnull' => false,
        ));
    	$table->setPrimaryKey(array('condition_id'));
    }
    
    protected function updateCoupon(Schema $schema)
    {
    	$table = $schema->getTable("plg_simple_coupon_coupon");
    	$table->addColumn('condition_action_type', 'smallint', array(
    			'notnull' => true,
        		'unsigned' => false,
        		'default' => 0,
    	));
    	
    }
}