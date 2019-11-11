<?php

$this->title = $content->title;

?>

<div class="site-content">

    <?php if ($content->content_type_id == Yii::$app->params['contentType']['article']) : ?>

        <div class="b-site-content__useful-art">Полезная информация</div>

    <?php endif; ?>

    <?= $content->content ?><br/>

    <div class="container">
        <?php if ($content->content_type_id == Yii::$app->params['contentType']['article']) : ?>

            <?= $otherArticlesBlock ?>

            <div class="b-site-content__wrap-btn">
                <?php if ($content['alias'] == 'how-to-choose-smetchik') { ?>
                    <a href="/how-to-choose-smetchik" class="btn art-left">Найти сметчика</a>
                    <a href="/order/create" class="btn art-right">Разместить заказ</a>
                <?php } elseif ($content['alias'] == 'how-much-does-smeta-cost') { ?>
                    <a href="/how-to-choose-smetchik" class="btn art-left">Зарегистрироваться</a>
                    <a href="/order/create" class="btn art-right">Разместить заказ</a>
                <?php } elseif ($content['alias'] == 'how-to-work-with-smetchik-freelancer') { ?>
                    <a href="/site/index" class="btn art-left">Найти сметчика</a>
                    <a href="/order/create" class="btn art-right">Разместить заказ</a>
                <?php } ?>
            </div>

        <?php endif; ?>

    </div>
</div>