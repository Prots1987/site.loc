<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\ InvalidParamException;

class ResetPasswordForm extends Model 
{
    public $password;//свойство для поля ввода пароля в форме
    private $_user;//закрытое свойство, в которое будем помещать объект найденного пользователя
    
    public function rules()
    {
        return [
            ['password', 'required']
        ];
    }
    
    public function attirbuteLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }
    
    public function __construct($key, $config = [])//получаем секретный ключ $key
    {
        if (empty($key) || !is_string($key)):// если ключ пустой
            throw new InvalidParamException('Ключ не может быть пустым');
        endif;
        $this->_user = User::findBySecretKey($key);//находим объект пользователя по ключу
        if (!$this->_user)://если объект не найден
            throw new InvalidParamException('Не верный ключ');
        endif;
        parent::__construct($config);
    }
    
    public function resetPassword()
    {
        //@var $user User указываем, что $user это объект модели User
        $user = $this->_user;
        $user->setPassword($this->password);//устанавливаем в свойство password_hash хеш введенного пароля
        $user->removeSecretKey();//устанавливаем свойство secret_key в null
        
        return $user->save();//если запись прошла успешно возвращаем true, иначе false
    }
    
}