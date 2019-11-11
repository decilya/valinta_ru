<?php

use yii\helpers\Html;
use app\assets\MaskAsset;

$this->title = 'Заказ';
$this->params['breadcrumbs'][] = ['label' => 'Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

MaskAsset::register($this);

?>

<div class="request-create">

    <div class="messageBlock">


          <div id="orderInfographic" class="b-info__about b-orderInfographic">
            <div class="clientsMessageHolder">
              <div class="container">
                <div class="infoBlock">
                  <img src="/img/newIcon/1.png" />
                  <p>
                    1. Зарегистрируйте <br/>
                    свой заказ.
                  </p>
                </div>
                <div class="infoBlock">
                  <img src="/img/newIcon/2.png" />
                  <p>
                    2. Дождитесь одобрения <br/>
                    и размещения Вашего заказа.
                  </p>
                </div>
                <div class="infoBlock">
                  <img src="/img/newIcon/3d.png" />
                  <p>
                    3. Выбирайте из предложений <br>
                    сметчиков и выполняйте работу <br>
                    в срок.
                  </p>
                </div>
              </div>
            </div>
          </div>


        <div class="container b-order__success">

            <h1 class="text-center">Вы успешно завершили регистрацию заказа.</h1>

            <p>Ваш заказ №<?= $order->id; ?> от <?= date('d.m.Y', $order->updated_at) ?> размещён на нашем портале
                сроком на 30 календарных дней.
                Информация о заказе разослана всем подходящим сметчикам, зарегистрированным на портале.</p>

            <p>
                <strong>Обратите внимание!</strong>
                На указанный в заказе адрес электронной почты <a href="mailto:<?= $order->email; ?>"><?= $order->email; ?></a> отправлено письмо для
                получения доступа на
                страницу редактирования заказа.
                В случае возникновения каких-либо вопросов или отсутствия письма в вашем почтовом ящике, пожалуйста,
                свяжитесь с нами по телефону: <?= Yii::$app->params['mainSystemPhone']; ?>
            </p>

        </div>
    </div>

</div>
