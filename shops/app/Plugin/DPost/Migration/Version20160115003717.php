<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160115003717 extends AbstractMigration
{

    private $createTables = array(
            'plg_product_post_queue' => array(
                    array('product_post_id', 'integer', array('autoincrement' => true), true),
                    array('regist_id', 'text', array('notnull' => true)),
                    array('product_id', 'integer', array('notnull' => true, 'unsigned' => false)),
                    array('queue_status', 'integer', array()),
                    array('create_date', 'datetime', array('notnull' => true, 'unsigned' => false)),
                    array('update_date', 'datetime', array('notnull' => true, 'unsigned' => false))
            ),
            'plg_product_push' => array(
                    array('product_push_id', 'integer', array('autoincrement' => true), true),
                    array('product_id', 'integer', array('notnull' => true)),
                    array('wait_count', 'integer', array()),
                    array('push_count', 'integer', array()),
                    array('click', 'integer', array()),
            )
    );

    private $updateTables = array(
            'plg_dpost_setting' => array(
                    array('product_flg', 'integer', array('notnull' => true, 'unsigned' => false, 'default' => 0))
            )
    );

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // テーブル追加
        $this->tableCreate($schema);

        // テーブル更新
        $this->updateTable($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->dropTable($schema);
    }

    private function tableCreate(Schema $schema) {

        foreach ($this->createTables as $key => $createTable) {

            $tableName = $key;

            $table = $schema->createTable($tableName);

            // カラム追加
            $arrPkey = array();
            foreach ($createTable as $columns) {

                if(count($columns) < 3) {
                    continue;
                }

                $table->addColumn($columns[0], $columns[1], $columns[2]);

                if(count($columns) == 4) {
                    $arrPkey[] = $columns[0];
                }
            }

            if(count($arrPkey) > 0) {
                $table->setPrimaryKey($arrPkey);
            }
        }

    }

    private function dropTable(Schema $schema) {

        foreach ($this->createTables as $key => $createTable) {

            $tableName = $key;

            $schema->dropTable($tableName);
        }

    }

    private function updateTable(Schema $schema) {

        foreach ($this->updateTables as $key => $createTable) {

            $tableName = $key;
            $table = $schema->getTable($tableName);

            // カラム追加
            $arrPkey = array();
            foreach ($createTable as $columns) {

                if(count($columns) < 3) {
                    continue;
                }

                $table->addColumn($columns[0], $columns[1], $columns[2]);

            }
        }
    }
}
