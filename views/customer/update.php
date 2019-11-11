<?php
/* @var  app\models\Customer $customer */

use app\models\Site;
use kartik\alert\Alert;
//use yii\bootstrap\Alert::gvggvfcj();
use yii\helpers\Html;

\app\assets\MaskAsset::register($this);
$this->title = 'Личный кабинет №' . $customer->real_id;

$this->registerJsFile('@web/js/userUpdate.js', [
    'depends' => 'yii\web\JqueryAsset'
], 3);

$this->registerJsFile('@web/js/customerOrdersController.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile(Yii::getAlias('@web') . "/js/extraPhoneNumbers.js");
?>

<!-- верстаки говорят, что это типа нужный для чего-то там им хак, хз, но пусть будет -->
<style>
    .glyphicon {
        display: none;
    }
</style>

<section id="customerUpdateCabinet">

    <?php
    if ((int)(\app\models\Auth::getUserType()) === (int)(\app\models\Auth::TYPE_CUSTOMER)) {
        echo $this->render('blocks/_customerCabinetNav', [
            'customer' => $customer,
            'switchCabinetNav' => 3
        ]);
    } ?>

    <div class="container">
        <?php
        $successFlashMessage = Yii::$app->session->getFlash('success');
        $errorFlashMessage = Yii::$app->session->getFlash('error');

        if ($successFlashMessage) {
            echo Alert::widget([
                'type' => Alert::TYPE_SUCCESS,
                'icon' => 'glyphicon glyphicon-ok-sign',
                'body' => $successFlashMessage,
                'showSeparator' => true,
                'delay' => 8000
            ]);
        }

        if ($errorFlashMessage) {
            echo Alert::widget([
                'type' => Alert::TYPE_DANGER,
                'icon' => 'glyphicon glyphicon-remove-sign',
                'body' => $errorFlashMessage,
                'showSeparator' => true,
                'delay' => 9000
            ]);
        }
        ?>

    </div>
    <main id="customerCabinet" class="container">
        <?php
        if ((int)(\app\models\Auth::getUserType()) === (int)(\app\models\Auth::TYPE_CUSTOMER)) {
            echo $this->render('blocks/_statisticsBlock.php', [
                'customer' => $customer,
            ]);
        } else { ?>
            <h1>Личный кабинет Заказчика №<?= $customer->real_id ?></h1>
        <?php } ?>

        <div class="user-update customer-update">

            <?php if (isset(Yii::$app->user->identity->is_admin)) {
                if (Yii::$app->user->identity->is_admin) { ?>
                    <div class="statusHolder">
                        <div class="container">
                            <table>
                                <tr>
                                    <td>
                                        <span>Статус профиля: <span><?= $customer->status ?></span></span>

                                        <a id="sendCustomerInstructions" data-id="<?= $customer->id ?>">Отправить ссылку
                                            для смены пароля</a>
                                    </td>
                                    <td>
                                        <a class="backToIndex top" href="/customer/admin-customers-list">Вернуться к
                                            списку</a>
                                    </td>
                                </tr>
                                <?php if ($customer->status_id == $customer::STATUS_REJECTED['val']) { ?>
                                    <tr>
                                        <td colspan="2">
                                            <span>Причина отклонения: <?= $customer->reason ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php }
            } ?>

            <div>
                <div class="messageBlock">
                    <?php if (!empty($msg)) { ?>

                        <?= Alert::widget([
                            'options' => ['class' => 'alert-' . $msg['status']],
                            'body' => $msg['body'],
                        ]); ?>

                    <?php } ?>
                </div>

                <?= $this->render('_form', [
                    'customer' => $customer,
                ]) ?>


            </div>
        </div>
    </main>
</section>
