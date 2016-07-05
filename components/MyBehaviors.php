<?php

namespace app\components;

use yii\base\Behavior;
use yii\web\Controller;//будет использоваться для вызова метода на событие EVENT_BEFORE_ACTION

class MyBehaviors extends Behavior 
{
    /*Добавляем три закрытых свойства, которые будут использоваться только в данном массиве
     *Значения эти свойств будут устанавливаться добавленными ниже сеттерами
    */
    private $_controller;// название контроллера
    private $_action;// название действия
    private $_removeUnderscore;// отправленной строки
    /* Добавляем сеттер и геттер для свойства $_controller
     * Сеттер - устанавливает любое значение для свойства. Дожен начинаться со слова set и с заглавной буквы
     * Геттер - читает значение свойства класса. Должен начинаться со слова get и с заглавной буквы
     */
    public function setController($value)//сеттер для свойства $_controller. Устанавливает в $_controller полученное из контроллера Behaviors
    {//значение $value, куда прикрепим это поведение
        $this->_controller = $value;
    }
    
    public function getController()//геттер для свойства $_controller. Получает значение свойства $_controller
    {
        return $this->_controller;
    }
    //добавляем сеттер и геттер для свойства $_action
    public function setAction($value)
    {
        $this->_action = $value;
    }
    public function getAction()
    {
        return $this->_action;
    }
    //добавляем сеттер и геттер для свойства $_removeUnderscore
    public function setRemoveUnderscore($value)
    {
        $this->_removeUnderscore = str_replace('_', '', $value);
    }
    public function getRemoveUnderscore()
    {
        return $this->_removeUnderscore;
    }
    //добавляем встроенный в класс yii\base\Behavior метод events() для обработки событий приложения:
    public function events()
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'beforeAction'//перед любым действием контроллеров Controller::EVENT_BEFORE_ACTION
            //вызывать метод beforeAction() из данного поведения
        ];
    }
    
    public function beforeAction()
    {
        if ($this->controller == 'main' && $this->action == 'search'):
            \Yii::$app->session->set('search', $this->removeUnderscore);
        endif;
    }
    
    
    
    
    
    
    
    
    
}

