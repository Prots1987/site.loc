<?php
/*
 * @property string $username
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

class AccountActivation extends Model {
    //@var $user app\models\User
    private $_user;
    
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
    //метод, который активирует нового пользователя
    public function activateAccount()
    {
        $user = $this->_user;//свойство $user - это объект пользователя
        $user->status = User::STATUS_ACTIVE;//статус активированного пользователя
        $user->removeSecretKey();//поле secret_key у пользователя равно NULL
        
        return $user->save();//сохранить и вернуть объект активированного пользователя
    }
    //геттер username, который возвращаем ник активированного пользователя
    public function getUsername()
    {
        $user = $this->_user;
        return $user->username;
    }
}
