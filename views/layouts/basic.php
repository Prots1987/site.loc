<?php

use app\assets\AppAsset;
use app\components\AlertWidget;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
    <head>
        <?= Html::csrfMetaTags() ?>
        <meta charset="<?= Yii::$app->charset ?>">
        <?php $this->registerMetaTag(['name' => 'viewport', 'content' => 'width = device-width, initial-scale = 1']); ?>
        <title><?= Yii::$app->name; ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="wrap">
            <?php NavBar::begin(
               [
                   'brandLabel' => 'Текстовое приложение',
               ]
            );
            if (!Yii::$app->user->isGuest)://если пользователь не Гость
                ?>
                <div class="navbar-form navbar-right">
                    <button class="btn btn-sm btn-default"
                        data-container="body"
                        data-toggle="popover"
                        data-trigger="focus"
                        data-placement="bottom"
                        data-title="<?= Yii::$app->user->identity['username'] ?>"
                        data-content="
                            <a href='<?= Url::to(['/main/profile']) ?>' data-method='post'>Мой профиль</a><br>
                            <a href='<?= Url::to(['/main/logout']) ?>' data-method='post'>Выход</a>
                        ">
                        <span class="glyphicon glyphicon-user"></span>
                    </button>
                </div>
            <?php
            endif;
            ActiveForm::begin(//- метка открытия формы
                [
                    'action' => ['/main/search'],//маршрут для формы, все данные из этой формы будут отправляться в действие Search контроллера Main
                    'method' => 'post',//метод отправки данных, в данном случае будет использоваться суперглобальный массив $_POST
                    'options'=> [
                        'class' => 'navbar-form navbar-right'//navbar-form(стиль формы), navbar-right(размещает форму справа в навигационной панели)
                    ],
                ]
            );
            //Объединим поле ввода для поиска и кнопку отправки в группу
            echo '<div class="input-group input-group-sm">';//добавим стиль iput-group-sm чтобы данная группа была размера sm
            //добавим поле ввода текста для поиска
            echo Html::input( // - сформирует тег <input>
                'type:text',// - тип поля текст
                'search', //- имя поля, которое будет доставать из $_POST
                '', //- пустое поле
                [
                    'placeholder' => 'Найти...',// отображать в пустом поле Найти
                    'class' => 'form-control',//bootstrap класс для формы ввода
                ]
            );
            echo '<span class="input-group-btn">';//приклеиваем кнопку к текстовому полю
            //добавим кнопку для поиска
            echo Html::submitButton(// сформирует тег <button>
                '<span class="glyphicon glyphicon-search"></span>', //отобразит bootstrap иконку для поиска
                [
                    'class' => 'btn btn-success',// btn (класс для кнопок), btn-success (класс зеленой кнопки)
                ]
            );
            echo '</span></div>';//закрываем открытые теги
            ActiveForm::end();//указываем метку для тега закрытия формы (т.е. </form>)
            
            $menuItems = [//список элементов меню
                
                [   
                    'label' => 'Главная <span class="glyphicon glyphicon-home"></span>', 
                    'url' => ['/main/index']       
                ],
                [
                    'label' => 'О проекте <span class="glyphicon glyphicon-question-sign"></span>', 
                    'url' => '',
                    'linkOptions' => [
                        'data-toggle' => 'modal',//атрибут для всех модальных окон, всегда = modal
                        'data-target' => '#modal',//id модального окна, разный для каждого окна
                        'style' => 'cursor:pointer; outline:none;'//
                    ],
                ],
                [
                    'label' => 'Из коробки <span class="glyphicon glyphicon-inbox"></span>',//название выпадающего списка и Bootstrap иконка
                    'items' => [//массив с элентами списка
                        '<li class="dropdown-header">Расширения</li>',//первый элемент списка (Заголовок)
                        '<li class="divider"></li>',//второй элемент списка (разделительная черта)
                        [//третий элемент списка (ссылка)
                            'label' => 'Перейти к просмотру', //название ссылки
                            'url' => ['/widget-test/index']//маршрут ссылки (WidgetTestController/actionIndex)
                        ],
                    ]
                ],               
            ];
            if (Yii::$app->user->isGuest) {//услвоие: если пользователь гость
                $menuItems[] = [
                    'label' => 'Регистрация',
                    'url'   => ['/main/reg']
                ];
                $menuItems[] = [
                    'label' => 'Войти',
                    'url'   => ['/main/login']
                ];
            } 
            echo Nav::widget([//виджет навигационного меню
                'options' => ['class' => 'navbar-nav navbar-right'],//стиль виджета
                'encodeLabels' => false, //не экранировать название элементов для вывода иконок
                'items' => $menuItems,//список элементов меню, которые описаны выше
            ]);
            Modal::begin([
                'header' => '<h2>Myproject</h2>',
                'id' => 'modal',
            ]);
            echo 'Мой проект';
            Modal::end();
            NavBar::end(); ?>
            
            <div class="container">
                <?= AlertWidget::widget() ?>
                <?= $content ?>
            </div>
        </div>
        
        <footer class="footer">
            <div class="container">
                <span class="badge">
                    <span class="glyphicon glyphicon-copyright-mark"></span>Mycompany <?= date('Y') ?>
                </span>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage(); ?>