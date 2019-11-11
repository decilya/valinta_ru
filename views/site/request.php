<?php use app\assets\MaskAsset;
use yii\bootstrap\Alert;
/** @var \app\models\Database{} $databasesTmp */
MaskAsset::register($this);
?>

<div class="site-request">

  <div class="smetawizardMessage">
    <div class="container">
      <figure class="b-smeta-img__wrapp">
        <img src="/img/checkM.png" class="b-smeta-img__right" alt=""/>
      </figure>
      <h2 class="b-smeta__title">
          Закажите доступ к программе SmetaWIZARD с нужным набором баз.<br/>
      </h2>
    </div>
  </div>

  <?php if(!empty($msg)) : ?>

    <div class="messageBlock">
      <div class="container">
        <?php

        echo Alert::widget([
            'options' => ['class' => 'b-centr alert-'.$msg['status']],
            'body' => $msg['body'],
        ]);

        ?>
      </div>

    </div>

  <?php endif; ?>

	<?= $this->render('blocks/_request-block', [
		'request' => $request,
		'model' => $model,
        'databasesTmp' => $databasesTmp
	]); ?>


</div>