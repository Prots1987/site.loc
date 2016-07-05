<?php

use yii\db\Migration;

/**
 * Handles the creation for table `profile`.
 */
class m160629_141824_create_profile extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('profile', [
            'user_id' => $this->primaryKey(),//поле для свзяи с полем id таблицы user
            'avatar'  => $this->string(),
            'first_name' => $this->string(32),
            'second_name' => $this->string(32),
            'middle_name' => $this->string(32),
            'birthday' => $this->integer(),
            'gender' => $this->smallInteger()       
        ]);
        $this->addForeignKey(// метод для создания связи
                'profile_user',// название связи
                'profile',// таблица, которую связываем
                'user_id',// поле, которое связываем
                'user',// таблица с которой связываем
                'id',// поле таблицы с которой связываем
                'cascade',// при удалении автоматически удаляется строка у связанной таблицы
                'cascade'// при изменении первичного ключа автоматически изменяется первычный ключ у связанной таблицы
        );
    }

    public function safeDown()//метод для отката миграции
    {
        $this->dropForeignKey('profile_user', 'profile');//удалить связь profile_user
        $this->dropTable('profile');//удалить таблицу PROFILE       
    }
}
