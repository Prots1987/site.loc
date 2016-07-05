<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string $avatar
 * @property string $first_name
 * @property string $second_name
 * @property string $middle_name
 * @property integer $birthday
 * @property integer $gender
 *
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birthday', 'gender'], 'integer'],
            [['avatar'], 'string', 'max' => 255],
            [['first_name', 'second_name', 'middle_name'], 'string', 'max' => 32],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'avatar' => 'Аватар',
            'first_name' => 'Имя',
            'second_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'birthday' => 'Дата рождения',
            'gender' => 'Пол',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function updateProfile()
    {   //если поле с текущим пользователем в таблице PROFILE уже добавлено, находим и открываем его для редактирования,
        //если поле текущего пользователя не найдено, создаем новый объект модели Profile
        $profile = ($profile = Profile::findOne(Yii::$app->user->id)) ? $profile : new Profile();
        
        $profile-> user_id = Yii::$app->user->id;//поле user_id равно id текущего пользователя
        $profile->first_name = $this->first_name;//поле first_name равно введенному имени в форме
        $profile->second_name = $this->second_name;//поле second_name равно введенной фамилии в форме
        $profile->middle_name = $this->middle_name;//поле middle_name равно введенному отчеству в форме
        
        return $profile->save() ? true : false;//если запис успешно создана или изменена, то возвращаем true, иначе возвращаем false
    }
    
    
}
