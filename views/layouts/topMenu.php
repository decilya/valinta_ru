<?php use app\models\Site; ?>

<div class="roww" style="margin-bottom: 34px;">

    <?php if (Yii::$app->user->isGuest || (!Yii::$app->user->isGuest && !(bool)Yii::$app->user->identity->is_admin)): ?>

    <div class="linksHolder clearfix">
        <div class="container">
            <div class="container_head">
                <?= Site::renderHeaderLinks(1) ?>

                <div class="cell b-top-nav__right <?= Yii::$app->controller->action->id !== 'login' && Yii::$app->controller->action->id !== 'agreement' && Yii::$app->controller->action->id !== 'request-agreement' ? 'gray' : ''; ?>">
                    <?php
                    if (Yii::$app->user->isGuest) {
                        echo "<a class='mainRegistrationLink' href='/site/registration'><i class=\"b-top-nav__ico-reg\"></i>Регистрация / </a>";
                        echo Site::renderHeaderLinks(4);
                    } else {

                        /** @var \app\models\Auth $auth */
                        $auth = \app\models\Auth::findOne(['id' => Yii::$app->user->identity->id]);

                        if (\app\models\Auth::getUserType() === \app\models\Auth::TYPE_CUSTOMER) {
                            echo "<a style='padding-right: 5px' href='/customer/update/" . Yii::$app->user->identity->id . "'><i class=\"b-top-nav__ico-entry\"></i>личный кабинет / </a>";
                        }

                        echo "<a class='logoutLink' href='/site/logout'>Выйти ";
                        if (\app\models\Auth::getUserType() === \app\models\Auth::TYPE_RCSC) {
                            echo "($auth->login)";
                        }

                        echo "</a>";
                    }
                    ?>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>