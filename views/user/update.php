<?php

use yii\bootstrap\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Личный кабинет №' . $model->real_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->real_id, 'url' => ['view', 'id' => $model->real_id]];
$this->params['breadcrumbs'][] = 'Update';

if (Yii::$app->user->identity->is_admin) {
    $this->registerJsFile('@web/js/userUpdate.js', [
        'depends' => 'yii\web\JqueryAsset',
        'position' => yii\web\View::POS_HEAD
    ]);
}
?>
<div class="user-update">

    <div class="titleHolder">
        <div class="container">
            <h2 style="display:block;"><?= Html::encode($this->title) ?></h2>
        </div>
    </div>

    <div class="statusHolder">
        <div class="container">
            <table>
                <tr>
                    <td>
                        <?php if (Yii::$app->user->identity->is_admin) : ?>

                            <span>Статус анкеты: <?= $status[$model->status_id]['title'] ?></span>
                            <br/>
                            <span><?= $model->status_msg ?></span>

                            <a id="sendInstructions" data-href="/user/send-instructions/<?= $model->id ?>">Отправить
                                ссылку
                                для смены пароля</a>

                            <a class="backToIndex top" href="/user/index">Вернуться к списку</a>

                        <?php endif; ?>

                        <?php if (Yii::$app->user->identity->is_user) : ?>

                            <span>Анкета: <?= ($model->status_id == Yii::$app->params['status']['pending']) ? 'на модерации' : $status[$model->status_id]['title'] ?> </span>

                        <?php endif; ?>


                    </td>
                    <td>
                        <a class="toggleVisibility" href="/user/change-visibility/<?= $model->id ?>"
                           class="<?= ((bool)$model->is_visible) ? 'btn btn-warning' : 'btn btn-success' ?>"><?= ((bool)$model->is_visible) ? 'Скрыть анкету' : 'Показать анкету' ?></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="container" style="padding-left: 10px">
            <?php if (!empty($model->reject_msg)) : ?>

                <br><span>Причина отклонения: <?= $model->reject_msg ?></span>

            <?php endif; ?>
        </div>
    </div>

    <div class="container">

        <div class="messageBlock">
            <?php if (!empty($msg)) : ?>

                <?= Alert::widget([
                    'options' => ['class' => 'alert_other alert-' . $msg['status']],
                    'body' => $msg['body'],
                ]); ?>

            <?php endif; ?>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
            'staticDBsContent' => $staticDBsContent,
            'is_user' => $is_user
        ]) ?>

        <?php if (Yii::$app->user->identity->is_user) : ?>
            <p class="update-info">
                <span class="update-info__star">&#042;</span>
                Обновление анкетных данных повышает рейтинг Вашей анкеты<br/>
                и вероятность получения заказа. <br/>
                Изменение рейтинга анкеты происходит один раз в сутки.
            </p>

            <div class="b-status__contacts">

                Статистика просмотров контактов <br/>

                <div class="b-status__wrap">
                    <span class="b-status__vert-line"></span>
                    <span class="b-status__period">за 30 дней:</span> <span data-range="30days"
                                                                            class="b-status__alltime_right"><?= $reports['30days'] ?></span><br/>
                    <span class="b-status__alltime">за всё время:</span> <span data-range="all"
                                                                               class="b-status__alltime_right"><?= $reports['all'] ?></span>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <?php if (Yii::$app->user->identity->is_user && !empty($requestBlock)) : ?>

        <?= $requestBlock ?>

    <?php endif; ?>

    <?php if (Yii::$app->user->identity->is_admin) : ?>

        <a class="backToIndex bottom bottomList" href="/user/index">Вернуться к списку</a>

    <?php endif; ?>

</div>
<script>
    $(document).ready(function() {
        $("a[href$='/register']").parent().parent().addClass("current")
    })
</script>
