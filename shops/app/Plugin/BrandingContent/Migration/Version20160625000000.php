<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160625000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
	    $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_branding_content');
    }

    protected function createPluginTable(Schema $schema)
    {
        $table = $schema->createTable("plg_branding_content");
        $table->addColumn('branding_id', 'integer');
        $table->addColumn('client_logo', 'text', array('notnull' => false));
        $table->addColumn('production_logo', 'text', array('notnull' => false));
        $table->addColumn('production_link', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('branding_id'));
    }}