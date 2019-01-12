<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 */
class Version20151208141537 extends AbstractMigration
{

    private $createTables = array(
            'plg_post_queue' => array(
                    array('post_id', 'integer', array('autoincrement' => true), true),
                    array('regist_id', 'text', array('notnull' => true)),
                    array('news_id', 'integer', array('notnull' => true, 'unsigned' => false)),
                    array('customer_id', 'integer', array('notnull' => true, 'unsigned' => false)),
                    array('queue_status', 'integer', array()),
                    array('create_date', 'datetime', array('notnull' => true, 'unsigned' => false)),
                    array('update_date', 'datetime', array('notnull' => true, 'unsigned' => false))
            ),
            'plg_customer_ex' => array(
                    array('customer_ex_id', 'integer', array('autoincrement' => true), true),
                    array('customer_id', 'integer', array('notnull' => true)),
                    array('regist_id', 'text', array('notnull' => true))
            ),
            'plg_news_ex' => array(
                    array('news_ex_id', 'integer', array('autoincrement' => true), true),
                    array('news_id', 'integer', array('notnull' => true)),
                    array('push_status', 'smallint', array()),
                    array('push_count', 'integer', array()),
                    array('click', 'integer', array()),
                    array('end_number', 'integer', array()),
            ),
            'plg_dpost_setting' => array(
                    array('setting_id', 'integer', array('autoincrement' => true), true),
                    array('project_id', 'text', array('notnull' => true)),
                    array('api_key', 'text', array('notnull' => true)),
            )
    );


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // テーブル追加
        $this->tableCreate($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->dropTable($schema);
    }

    public function postUp(Schema $schema) {

        $app = new \Eccube\Application();
        $app->boot();

        // Update
        $select = "select max(p.page_id) as page_id_max from dtb_page_layout p";
        $pageIds = $this->connection->executeQuery($select)->fetchAll();

        $pageIdMax = $pageIds[0]['page_id_max'] + 1;

        $insert = "insert into dtb_page_layout ";
        $insert .= "(page_id, device_type_id, page_name, url, file_name, edit_flg, create_date, update_date, meta_robots)";
        $insert .= " values (";
        $insert .= ":pageIdMax, 10, 'MYページ/通知設定', 'mypage_dpost', 'Mypage/dpost', 2, current_timestamp, current_timestamp, 'noindex')";

        $this->connection->executeUpdate($insert, array(':pageIdMax'=>$pageIdMax));
    }

    public function postDown(Schema $schema) {

        $delete = "delete from dtb_page_layout where url = 'mypage_dpost'";
        $this->connection->executeUpdate($delete);
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
}
