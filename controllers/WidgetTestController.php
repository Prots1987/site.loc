<?php

namespace app\controllers;

use Yii;

class WidgetTestController extends BehaviorsController
{
    public function actionIndex()
    {
        //return Yii::$app->response->sendFile('files/hello.txt')->send();
        return $this->render('index',[
            
        ]);
    }

}
