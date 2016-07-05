<?php
/* @var $this yii\web\View */
use app\components\FirstWidget;
use yii\bootstrap\Modal;
use yii\jui\DatePicker;
?>
<h1>main/index</h1>

<p>
    <?= $hello ?>
</p>
<?= FirstWidget::widget([
    'a' => 33,
    'b' => 67,
]); ?>
<?php 
Modal::begin([
    'header' => '<h2>Привет Мир<h2>',
    'toggleButton' => ['label' => 'Нажми'],
]);
echo 'Это компонент модального окна';
Modal::end();
?>

<?php 
echo DatePicker::widget([
    'attribute' => 'from_date',
    'language' => 'ru',
    //'dateFormat' => 'yyyy-MM-dd',
]);
?>
