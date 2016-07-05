<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SendEmailForm extends Model
{
    public $email;
    
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],//убрать пробелы по краям
            ['email', 'required'],//обязательно для заполнения
            ['email', 'email'],//может быть только email адресом
            ['email', 'exist',//введенный email должен быть в таблице USER и пользователь с данными email должен быть активирован
                'targetClass' => User::className(),
                'filter' => [
                    'status' => User::STATUS_ACTIVE
                ],
                'message' => 'Данный email не зарегистрирован'
            ],
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'email' => 'Емайл'
        ];
    }
    
    public function sendEmail()
    {
        //@var $user User
        $user = User::findOne(
                [
                    'status' => User::STATUS_ACTIVE,
                    'email'  => $this->email
                ]
        );
        
        if ($user)://если пользователь найден
            $user->generateSecretKey();//создать и присвоить свойству secret_key пользователя, секретный ключ с временной меткой
            if ($user->save())://сохранить ключ в БД. Если все выполнено, отправить письмо пользователю
                return Yii::$app->mailer->compose('resetPassword', ['user' => $user])//используем представление resetPassword, 
                    //которое сейчас создадим и передадим в него найденный объект пользователя
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.'(отправленно роботом)'])//отправлен от кого
                    ->setTo($this->email)//отправить кому (введенный в форме email)
                    ->setSubject('Сброс пароля для'.Yii::$app->name)//тема письма
                    ->send();
            endif;
        endif;
        //Если какое-то условие не выполнено возвращаем false
        return false;
    }
    
}
