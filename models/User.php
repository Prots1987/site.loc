<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property integer $status
 * @property string $auth_key
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $secret_key
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 10;
    
    public $password;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'status'], 'required'],
            ['email', 'email'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['password', 'required', 'on' => 'create'],
            ['username', 'unique', 'message' => 'Это имя занято'],
            ['email', 'unique', 'message' => 'Эта почта уже зарегистрирована'],
            ['secret_key', 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Ник',
            'email' => 'Email',
            'password' => 'Password Hash',
            'status' => 'Статус',
            'auth_key' => 'Auth Key',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }
    /*СВЯЗИ*/
    public function getProfile()//свзять с таблицей profile
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
    
    /*ПОВЕДЕНИЯ*/
    public function behaviors() 
    {
        return [
            TimestampBehavior::className()
        ];
    }
    /*ПОИСК*/
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username
        ]);
    }
    
    public function findSecretKey($key)
    {
        if (!static::SecretKeyExpire($key)) {//если метод isSecretKeyExpire() вернул false
            return null;//ошибка, возвращем null
        }
        return static::findOne(//если метод isSecretKeyExpire() вернул true, находим объект пользователя
            [
                'secret_key' => $key//у которого поле secret_key равняется переданному ключу $key
            ]
        );
    }
    
    /*ХЕЛПЕРЫ*/
    public function generateSecretKey()
    {
        $this->secret_key = Yii::$app->security->generateRandomString().'_'.time();
    }
    
    public function removeSecretKey()
    {
        $this->secret_key = null;
    }
    
    public static function isSecretKeyExpire($key)
    {
        if (empty($key)) {//если переменная $key пустая
            return false;//ошибка, возвращаем false
        } 
        $expire = Yii::$app->params['secretKeyExpire'];//переменная $expire равна сроку действия секретного ключа, параметр берется из params.ph
        $parts = explode('_', $key);//разиваем строку на массив(разделитель - знак подчеркивания), где первый элемент будет сгенерированный ранее ключ, 
        //второй элемент будет временем создания ключа
        $timestamp = (int) end($parts);//помещаем в переменную $timestamp последний элемент массива $parts, т.е. время создания ключа
        return $timestamp + $expire >= time();//складываем время создания ключа и время действия ключа, и если полученное значение больше либо равно 
        //текущему времени => возвращаем true, иначе => срок действия ключа истек и возвращаем false
    }
    
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    
    
    /*Аутентификация пользователей*/
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}
