<?php

namespace app\controllers;

use Yii;
use app\models\RegForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Profile;
use app\models\SendEmailForm;
use app\models\ResetPasswordForm;
use yii\base\ InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\AccountActivation;

class MainController extends BehaviorsController
{
    public function actionIndex()
    {
        $hello = "Привет Мир";
        
        return $this->render('index', [
            'hello' => $hello,
        ]);
    }
    
    public function actionProfile()
    {
        $model = ($model = Profile::findOne(Yii::$app->user->id)) ? $model : new Profile();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()):
            if ($model->updateProfile()):
                Yii::$app->session->setFlash('success', 'Профиль изменен');
            else:
                Yii::$app->session->setFlash('error', 'Профиль не изменен');
                Yii::error('Ошибка записи. Профиль не изменен');
                return $this->refresh();
            endif;
        endif;
        
        return $this->render('profile', [
            'model' => $model
        ]);
    }
    
    public function actionReg()
    {
        //помещаем в $emailActivation значение свойства emailActivation из пользовательских параметров
        $emailActivation = Yii::$app->params['emailActivation'];
        /*
         * Если $emailActivation = true, создаем новый объект из модели RegForm используя сценарий emailActivation в этой модели
         * Если $emailActivation = false, то при создании нового объекта не используем сценарий emailActivation
         */
        $model = $emailActivation ? new RegForm(['scenario' => 'emailActivation']) : new RegForm();
        

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->reg()) {//из метода reg() возвращаются: объект пользователя(если он сохранился в БД) или null. Загружаем возвращаемое значение в $user
                if ($user->status === User::STATUS_ACTIVE) {//если метод reg() вернул объект сохраненного пользователя, проверяем чтобы его статус был активированный пользователь
                    if (Yii::$app->getUser()->login($user)) {//если статус польователя активированный, проводим аутентификацию пользователя метдом login() класса yii\web\User
                        return $this->goHome();//если аутентификация прошла успешно переходим на главную страницу сайт
                    }
                } else {//если статус пользователя имеет значение не активированного пользователя, т.е. был использован сценарий emailActivation в модели RegForm
                    if ($model->sendActivationEmail($user)) {//пробуем отправить письмо с ключом для активации
                        //если письмо отправлено => выводим сообщение об успехе
                        Yii::$app->session->setFlash('success', 'Письмо отправлено на email<strong>' .Html::encode($user->email).'</strong>(проверте папку спам).');
                    } else {
                        //если письмо не отправлено => выводим сообщение об ошибке
                        Yii::$app->session->setFlash('error', 'Ошибка. Письмо не отправлено');
                        Yii::error('Ошибка отправки письма');
                    }
                    return $this->refresh();//обновляем представление reg
                }
            } else {//если пользователь не записан 
                Yii::$app->session->setFlash('error', 'Возникла ошибка при регистрации');//выводим сообщение об ошибке
                Yii::error('Ошибка при регистрации');//пишем ошибку в журнал
                return $this->refresh();//обновляем текущую страницу
            }
        }
        
        return $this->render('reg', [
            'model' => $model
        ]);
    }
    /* Создаем новое действие для активации аккаунта ActivateAccount, которое 
     * будет выполняться, когда пользователь перейдет по ссылке в письме. Помним,
     * что в ссылке передается переменная $key с секретным ключом:
     */
    public function actionActivateAccount($key)
    {
        try {
            /*Создаем новый объект AccountActivation. Перед созданием запустится
             * конструктор в модели AccountActivation и проверит ключ, 
             * если в ключе ошибка => вызывается исключение InvalidParamException
             */
            $user = new AccountActivation($key);
        }
        catch (InvalidParamException $e) {//если есть исключение InvalidParamException
            /* взываем исключение BadRequestHttpException "Плохой запрос (код 400)" 
             * с сообщением исключения InvalidParamException из конструктора в модели
             * AccountActivation
             */
            throw new BadRequestHttpException($e->getMessage());
        }
        //Если объект модели AccountActivation был создан:
        if ($user->activateAccount()) {//активируем пользователя
            Yii::$app->session->setFlash('success', 'Активация прошла успешно <strong>'.Html::encode($user->name).'</strong>');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка активации');//выводим сообщение об ошибке
            Yii::error('Ошибка при активации');//пишем ошибку в журнал
        }
        return $this->redirect(Url::to(['/main/login']));//делаем переход на страницу входа
    }
    
    
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {//если пользователь уже авторизован (не является гостем)
            return $this->goHome();//перейти на гланую страницу сайта
        }
        //Если пользователь является гостем, выполнить аутентификацию и авторизацию данного пользователя
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        
        return $this->render('login', [
            'model' => $model
        ]);
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();//метод logout() класса yii\web\User устанавливает пользователя в качестве гостя, очищает информацию о пользователе из сесси и куки
  
        return $this->redirect(['/main/index']);//после выхода, переходим в действие Index контроллера Main
    }
    
    //создаем новое действие Search
    public function actionSearch()
    {
        //$search = Yii::$app->request->post('search');
        $search = Yii::$app->session->get('search');
        Yii::$app->session->remove('search');
        
        if ($search) {
            Yii::$app->session->setFlash(
                    'success',
                    'Результат поиска'
            );
        } else {
            Yii::$app->session->setFlash(
                    'error',
                    'Не заполнена форма поиска'
            );      
        }
        
        return $this->render('search', [
                    'search' => $search,
        ]);
    }
    
    public function actionSendEmail() {
        $model = new SendEmailForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->sendEmail())://если письмо отправлено
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте ваш email');
                    return $this->goHome();//переходим на главную страницу сайта
                else:
                    Yii::$app->getSession()->setFlash('error', 'Нельзя сбросить пароль');
                endif;
            }
        }

        return $this->render('sendEmail', [
                    'model' => $model,
        ]);
    }
    
    
    public function actionResetPassword($key) 
    {
        try {
            $model = new ResetPasswordForm($key);
        }
        catch(InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage()); 
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                Yii::$app->getSession()->setFlash('warning', 'Пароль изменен');
                
                return $this->redirect(['/main/login']);//перенаправление на страницу входа
            }
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

}
