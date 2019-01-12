<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151224221937 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createDtbExternalPlugin($schema);
        $this->createDtbExternalOrderPayment($schema);
        $this->createDtbExternalPaymentMethod($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // dtb_paymentは受注データと紐付いているため削除しない
        $this->deleteFromDtbPayment();
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_external_plugin');
        $schema->dropTable('plg_external_payment_method');
        $schema->dropTable('plg_external_order_payment');
    }

    public function postUp(Schema $schema)
    {
        $app = new \Eccube\Application();
        $app->boot();
        $config = $app['config'];

        //yamlファイルのパース
        $config = Yaml::parse(__DIR__ . '/../config.yml');

        $pluginName  = $config['name'];
        $pluginCode = $config['code'];

        // Insert module information into plg_external_plugin
        $datetime = date('Y-m-d H:i:s');
        $insert = "INSERT INTO plg_external_plugin(
                            plugin_code, plugin_name, create_date, update_date)
                    VALUES ('$pluginCode', '$pluginName', '$datetime', '$datetime'
                            );";
        $this->connection->executeUpdate($insert);
    }

	//テレコム決済設定テーブル作成
    protected function createDtbExternalPlugin(Schema $schema)
    {
        $table = $schema->createTable("plg_external_plugin");
        $table->addColumn('plugin_id', 'integer', array(
            'autoincrement' => true,
        ));

        $table->addColumn('plugin_code', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('plugin_name', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('sub_data', 'text', array(
            'notnull' => false,
        ));

        $table->addColumn('auto_update_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('plugin_id'));
    }
    protected function createDtbExternalOrderPayment(Schema $schema)
    {
        $table = $schema->createTable("plg_external_order_payment");
        $table->addColumn('order_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('memo01', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo02', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo03', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo04', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo05', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo06', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo07', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo08', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo09', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo10', 'text', array(
            'notnull' => false,
        ));

        $table->setPrimaryKey(array('order_id'));

    }

    protected function createDtbExternalPaymentMethod(Schema $schema)
    {
        $table = $schema->createTable("plg_external_payment_method");

        //id
        $table->addColumn('payment_id', 'integer', array(
            'notnull' => true,
        ));

        // method
        $table->addColumn('payment_method', 'text', array(
            'notnull' => true,
        ));

        // delete flg
        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        // create date
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        // update date
        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('memo01', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo02', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo03', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo04', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo05', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo06', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo07', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo08', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo09', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('memo10', 'text', array(
            'notnull' => false,
        ));
        // plugin_code
        $table->addColumn('plugin_code', 'text', array(
            'notnull' => false,
        ));

        $table->setPrimaryKey(array('payment_id'));
    }

    protected function deleteFromDtbPayment()
    {
        $select = "SELECT p.payment_id FROM plg_external_payment_method as external 
                JOIN dtb_payment as p ON external.payment_id = p.payment_id
                WHERE external.plugin_code =  'ExternalPaymentGateway'";
        
        $paymentIds = $this->connection->executeQuery($select)->fetchAll();
        $ids = array();
        
        foreach ($paymentIds as $item){
            $ids[]=$item['payment_id'];
        }        
        
        if (!empty($ids)){
            $param = implode(",", $ids);
            $update = "UPDATE dtb_payment SET del_flg = 1 WHERE payment_id in ($param)";
            $this->connection->executeUpdate($update);
        }
        
    }

    function getExternalPaymentCode()
    {
        $config = \Eccube\Application::alias('config');

        return $paymentCodes;
    }
}
