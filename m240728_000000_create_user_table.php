<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240728_000000_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'password' => $this->string(255)->notNull(),
            'email' => $this->string(255)->unique(),
            'name' => $this->string(255),
            'role' => $this->string(50)->defaultValue('user'),
            'type' => $this->string(50)->defaultValue('user'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login_at' => $this->integer(),
        ]);

        // Create indexes
        $this->createIndex('idx-user-username', '{{%user}}', 'username');
        $this->createIndex('idx-user-email', '{{%user}}', 'email');
        $this->createIndex('idx-user-status', '{{%user}}', 'status');
        $this->createIndex('idx-user-role', '{{%user}}', 'role');

        // Insert default admin user
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'password' => Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@janmat.com',
            'name' => 'System Administrator',
            'role' => 'admin',
            'type' => 'admin',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
