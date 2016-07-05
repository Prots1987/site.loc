<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model 
{
    
    public $username;
    public $password;
    public $email;
    public $rememberMe = true;//по умолчанию стоит галочка
    public $status;
    
    private $_user = false;//описание данной переменной см.урок №14 "Регистрация и аутентификация пользователей в Yii2"
    
    public function rules()
    {
        return [
            [['username','password'], 'required', 'on' => 'default'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword']
        ];
    }  
    
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильное имя пользователя или пароль');
            }
        }
    }
    
    public function getUser()
    {
        if ($this->_user === false) {//если $_user равна false
            $this->_user = User::findByUsername($this->username);//найти пользователя с таким именем. Метод findByUsername() создали в модели User
        }
        
        return $this->_user;//вернуть объект найденного пользователя
    }
    
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }
    
    public function login()
    {
        if ($this->validate()) {
            $this->status = ($user = $this->getUser()) ? $user->status : User::STATUS_NOT_ACTIVE;
            if ($this->status === User::STATUS_ACTIVE) {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}