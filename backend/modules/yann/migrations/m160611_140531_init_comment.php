<?php

use yii\db\Schema;
use yii\db\Migration;

class m160611_140531_init_comment extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'id'         => Schema::TYPE_PK,
            'hash'       => 'char(32) NOT NULL',
            'content'    => Schema::TYPE_TEXT . ' NOT NULL',
            'parent_id'  => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'level'      => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 1',
            'created_by' => Schema::TYPE_INTEGER,
            'updated_by' => Schema::TYPE_INTEGER,
            'status'     => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 1',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'username'   => Schema::TYPE_STRING,
        ], $tableOptions);
    }

    /**
     * Drop table `comment`
     */
    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
