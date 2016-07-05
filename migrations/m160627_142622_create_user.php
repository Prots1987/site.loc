<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user`.
 */
class m160627_142622_create_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),//id пользователя
            'username' => $this->string(),//имя пользователя
            'email' => $this->string(),//почта пользователя
            'password_hash' => $this->string(),//хэш введенного пароля
            'status' => $this->smallInteger(),//статус пользователя(бан, не активирован, активирован)
            'auth_key' => $this->string(32),//уникальный ключ для кнопки "запомнить меня"
            'created_at' => $this->integer(),//дата и время регистрации пользователя
            'updated_at' => $this->integer(),//дата и время изменения данных пользователя       
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
