<?php

namespace threedgroup\craftrest\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%craftrestapi_token}}')) {
            // create the products table
            $this->createTable('{{%craftrestapi_token}}', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'name' => $this->char(128),
                'token' => $this->char(64)->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->tableExists('{{%craftrestapi_token}}')) {
            $this->dropTable('{{%craftrestapi_token}}');
        }
    }
}
