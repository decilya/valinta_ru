<?php

/**
 * @var  yii\web\View $this
 * @var  mixed $contentSearch
 * @var  mixed $contentResults
 *
 */

$this->registerMetaTag(['content' => Yii::t('app', 'Валинта - это актуальная база специалистов-сметчиков по расчету и составлению сметной документации в Санкт-Петербурге и других регионах Российской Федерации.'), 'name' => 'description']);

$this->registerMetaTag(['content' => Yii::t('app', 'валинта
valinta
ищу сметчика
поиск сметчиков
удаленный сметчик
резюме сметчик
составление смет
стоимость составления сметы
составление сметной документации'), 'name' => 'keywords']);

$this->title = 'Поиск сметчиков для разработки и составления смет';

$this->registerJsFile('@web/js/userSearchFilters.js', [
    'depends' => 'yii\web\JqueryAsset'
]);
?>

<?php if (!Yii::$app->request->isAjax) : ?>

    <div class="site-index">

        <?= $contentSearch ?>


        <?= $contentResults ?>

    </div>

<?php endif; ?>


<?php if (Yii::$app->request->isAjax) : ?>

    <?= $contentResults ?>

<?php endif; ?>