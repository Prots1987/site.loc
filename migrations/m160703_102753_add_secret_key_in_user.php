<?php

use yii\db\Migration;

class m160703_102753_add_secret_key_in_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'secret_key', $this->string());
    }

    public function down()
    {
        $this->dropColumn('user', 'secret_key');
    }
}
