<?php
/*
 * @var $user app\models\User
 */
use yii\helpers\Html;

echo 'Привет'.Html::encode($user->username).'. ';//приветствие пользователя

/* Ссылка с ключом, перейдя по которой пользоватлеь перейдет в действие 
 * ActivateAccount контроллера Main и через $_GET передаст секретный кллюч $key
*/
echo Html::a('Для активации аккаунта перейдите по этой ссылке', 
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/main/activate-account',
            'key' => $user->secret_key
        ]
));