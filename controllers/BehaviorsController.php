<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\MyBehaviors;

class BehaviorsController extends Controller 
{
    public function behaviors()
    {       
        return [
            'access' => [//название поведения
                'class' => AccessControl::className(),//класс поведения будет фильтр контроля доступа AccessControl
                'rules' => [
                    [
                        'allow' => true,// правило разрешить 
                        'controllers' => ['main'],// для контроллера Main
                        'actions' => ['reg', 'login', 'activate-account'],// для действий actionReg(), actionLogin(), actionActivateAccount()
                        'verbs' => ['GET', 'POST'],// с запросами методами GET и POST
                        'roles' => ['?']// доступ пользователям, которые являются гостями. Значк ? это метка пользователя с роль "гост
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['main'],
                        'actions' => ['profile'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@']
                    ],
                    //добавляем правило доступа для выхода пользователя
                    [
                        'allow' => true,// правило "разрешить"
                        'controllers' => ['main'],// для контроллера MainController
                        'actions' => ['logout'],// для действия actionLogout
                        'verbs' => ['POST'],// метод запроса POST
                        'roles' => ['@']// доступ идентифицированным пользователям
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['main'],
                        'actions' => ['index', 'search', 'send-email', 'reset-password']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['widget-test'],
                        'actions' => ['index', 'search']
                    ],
                ],
            ],
            'removeUnderscore' => [//имя поведения
                'class' => MyBehaviors::className(),//класс поведения
                'controller' => Yii::$app->controller->id,//в сеттер setController($value) поведения MyBehaviors отправляется название текущего 
                //контроллера (если контроллер MainController => отправиться 'main')
                'action' => Yii::$app->controller->action->id,//в сеттер setAction($value) поведения MyBehaviors отправляется название текущего 
                //действия (если действие actionSearch => отправиться 'search')
                'removeUnderscore' => Yii::$app->request->get('search')//в сеттер setRemoveUnderscore($value) поведения MyBehaviors отправляется 
                //переменная search из глобального массива $_SET
            ],
        ];
    }
    
}