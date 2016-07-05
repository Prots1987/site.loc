<?php
/*
 * @var $user \app\models\User
 */
use yii\helpers\Html;

echo 'Привет'.Html::encode($user->username).'. ';//строка приветствия пользователя
echo Html::a('Для смены пароля перейдите по этой ссылке.',//ссылка с ключом перейдя по которой польователь перейдет в действие
        //ResetPassord контроллера Main и через $_GET передаст секретный ключ $key
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/main/reset-password',
            'key' => $user->secret_key
        ]
    )
);