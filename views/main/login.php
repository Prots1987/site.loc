<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */
/* @var $form ActiveForm */
?>
<div class="main-login">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput(); ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
    
        <div class="form-group">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    
    <?= Html::a('Забыли пароль?', ['main/send-email']) ?>

</div><!-- main-login -->
