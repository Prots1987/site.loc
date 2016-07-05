<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegForm extends Model 
{
    
    public $username;
    public $email;
    public $password;
    public $status;
    
    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password'], 'required', 'message' => 'Данное поле должно быть заполнено'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['password', 'string', 'min' => 6, 'max' => 255],
            ['username', 'unique',
                'targetClass' => User::className(),
                'message' => 'Это имя уже занято'
            ],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => User::className(),
                'message' => 'Эта почта уже занята'
            ],
            ['status', 'default', 'value' => User::STATUS_ACTIVE, 'on' => 'default'],
            ['status', 'in', 'range' => [
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE
            ]],
            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE, 'on' => 'emailActivation'],
        ];
    } 
    
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'email'    => 'Адрес электронной почты',
            'password' => 'Пароль',
        ];
    }
    
    public function reg()
    {
        $user = new User();//создаем новый объект $user модели User и заполняем этот объект следующими данными (которые ниже)
        $user->username = $this->username;//атрибут username объекта $user равен введенному имени пользователя
        $user->email = $this->email;//атрибут email объекта $user равен введенному email
        $user->status = $this->status;//атрибут status объекта $user пока по умолчанию равен 10 (активированный пользователь)
        $user->setPassword($this->password);//вызываем хелпер setPassword() из модели User, который сформирует из введенного пароля хеш и присвоит его атрибуту $user->password_hash
        $user->generateAuthKey();//вызываем хелпер generateAuthKey() из модели User, который сгенерирует случайное число и присвоит его атрибуту $user->auth_key
        if ($this->scenario === 'emailActivation')://если используется сценарий emailActivation
            $user->generateSecretKey();//записать в поле secret_key случайную строку и текущее время. Этот ключ будет использоваться для активации
        endif;
        
        return $user->save() ? $user:null;//сохраняем нового пользователя в базе данных $user->save() и если пользователь сохранился, возвращаем его объект, если нет тогда возвращаем null
    }
    
    public function sendActivationEmail($user)
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.'(отправлено роботом)'])//отправлено от кого 
                ->setTo($this->email)//отправить кому (введеный в форме email)
                ->setSubject('Активация для'.Yii::$app->name)//тема письма
                ->send();
    }
    
    
}