<?php

use yii\db\Schema;
use yii\db\Migration;

class m160611_140457_init_comment_hash extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment_hash}}', [
            'id'         => Schema::TYPE_PK,
            'hash'       => 'char(32) NOT NULL',
            'url'        => Schema::TYPE_TEXT . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    /**
     * Drop table `comment`
     */
    public function down()
    {
        $this->dropTable('{{%comment_hash}}');
    }
}
