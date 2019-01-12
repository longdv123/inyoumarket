<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160315110000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_contacts');
        $schema->dropTable('plg_contacts_reply');
        $schema->dropTable('mtb_plg_contact_status');
    }

    protected function createPluginTable(Schema $schema)
    {
        // plg_contactsを作成
        $table = $schema->createTable("plg_contacts");
        $table->addColumn('contact_id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('contact_ins_time', 'datetime', array('notnull' => true));
        $table->addColumn('contact_del_time', 'datetime', array('notnull' => false));
        $table->addColumn('contact_status', 'smallint', array('notnull' => false));
        $table->addColumn('contact_name01', 'text', array('notnull' => true));
        $table->addColumn('contact_name02', 'text', array('notnull' => true));
        $table->addColumn('contact_kana01', 'text', array('notnull' => false));
        $table->addColumn('contact_kana02', 'text', array('notnull' => false));
        $table->addColumn('contact_zip01', 'string', array('length' => 3,'notnull' => false));
        $table->addColumn('contact_zip02', 'string', array('length' => 4,'notnull' => false));
        $table->addColumn('contact_pref', 'smallint', array('notnull' => false));
        $table->addColumn('contact_addr01', 'text', array('notnull' => false));
        $table->addColumn('contact_addr02', 'text', array('notnull' => false));
        $table->addColumn('contact_tel01', 'string', array('length' => 5,'notnull' => false));
        $table->addColumn('contact_tel02', 'string', array('length' => 4,'notnull' => false));
        $table->addColumn('contact_tel03', 'string', array('length' => 4,'notnull' => false));
        $table->addColumn('contact_email', 'string', array('length' => 255,'notnull' => true));
        //$table->addColumn('contact_kind', 'text', array('notnull' => true));
        $table->addColumn('contact_contents', 'text', array('notnull' => true));
        $table->addColumn('contact_memo', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('contact_id'));

        // plg_contacts_replyを作成
        $table = $schema->createTable("plg_contacts_reply");
        $table->addColumn('reply_id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('reply_ins_time', 'datetime', array('notnull' => true));
        $table->addColumn('reply_del_time', 'datetime', array('notnull' => false));
        $table->addColumn('reply_contact_id', 'integer', array('notnull' => true));
        $table->addColumn('reply_member_id', 'integer', array('notnull' => true));
        $table->addColumn('reply_subject', 'text', array('notnull' => true));
        $table->addColumn('reply_contents', 'text', array('notnull' => true));
        $table->addColumn('reply_memo', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('reply_id'));

        // mtb_plg_contact_statusを作成
        $table = $schema->createTable("mtb_plg_contact_status");
        $table->addColumn('id', 'smallint', array('unsigned' => false, 'notnull' => true));
        $table->addColumn('name', 'text', array('notnull' => false));
        $table->addColumn('rank', 'smallint', array('notnull' => true));
        $table->setPrimaryKey(array('id'));

    }
}