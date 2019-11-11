<?php
use app\models\Site;
use app\models\User;
use yii\helpers\Html;

//site-index
$cityMatch = (!empty($qp['city']) && (bool)$item['has_city'] && ($qp['city'] == $item['city_id'])) ? true : false;

$qpCheck = Site::massImplode(['professions', 'normbases', 'smetadocs'], $qp);
?>

<div class="itemRow" data-id="<?= $item['id'] ?>" data-position="<?= $cnt ?>"
     data-percent="<?= (!empty($matchesPercentArr[$item['id']])) ? $matchesPercentArr[$item['id']] : '0' ?>">

    <div class="firstRow">
        <div class="nameHolder">
            <h3>
                <?= Html::encode($item['fio']) ?>
            </h3>
            <div class="updateDateHolder">
                Анкета обновлена: <span><?= User::renderDate($item) ?></span>
            </div>
            <div class="showContactsBlock">
                <div class="imgHolder"></div>
                <div class="linkHolder">
                    <a class="showContacts">
                        Открыть контакты
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($item['ipap_attestat_id'])) : ?>
            <div class="ipapHolder">
                <p><label>Аттестат ИПАП: </label> <?= Html::encode($item['ipap_attestat_id']) ?></p>
                <a target="_blank"
                   href="http://ipap.ru/svedeniya-ob-ipap/vydavaemye-dokumenty?attId=<?= Html::encode($item['ipap_attestat_id']) ?>#registrySearchForm"
                   class="checkIpap">проверить подлинность</a>
            </div>
        <?php endif; ?>

    </div>

    <div class="secondRow">

        <?php if (!empty($item['price'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <label><b>Стоимость работ:</b></label>
                </div>
                <div class="valueRow">
                    <span class="price">от&nbsp;<?= Html::encode($item['price']) ?>&nbsp;руб.</span>
                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($item['experience'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <strong><b>Образование и опыт работы:</b> </strong>
                </div>
                <div class="valueRow experience<?= (Yii::$app->request->isAjax) ? ' ajax' : '' ?>">
                    <?= preg_replace('/<br>[ (<br>)]*<br>/', '<br>', preg_replace('/([\r\n]+)/', '<br>', trim($item['experience']))) ?>
                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($qpCheck['professions']) || !empty($qpCheck['normbases']) || !empty($qpCheck['smetadocs'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <strong><b>Соответствие запросу:</b> </strong>
                </div>
                <div class="valueRow">
                    <span class="percent <?= User::determinePercentColor($matchesPercentArr[$item['id']]) ?>">
                        <?= (!empty($matchesPercentArr[$item['id']])) ? $matchesPercentArr[$item['id']] : '0' ?>%
                    </span>
                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($related[$item['id']]['professions'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <label><b>Профессиональная область:</b></label>
                </div>
                <div class="valueRow matchBlock">

                    <?php
                    if (!empty($qp) && !empty($qp['professions'])) {
                        echo User::printSelect2Items($related[$item['id']]['professions'], $staticDBsContent['professions'], $qp['professions']);
                    } else {
                        $cnt = 0;
                        foreach ($related[$item['id']]['professions'] as $items) {
                            $delimiter = (++$cnt != count($related[$item['id']]['professions'])) ? '; ' : '';
                            echo '<span class="default item">' . $staticDBsContent['professions'][$items]['title'] . $delimiter . '</span>';
                        }
                    }
                    ?>

                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($related[$item['id']]['normbases'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <label><b>Нормативные базы:</b></label>
                </div>
                <div class="valueRow matchBlock">

                    <?php
                    if (!empty($qp) && !empty($qp['normbases'])) {
                        echo User::printSelect2Items($related[$item['id']]['normbases'], $staticDBsContent['normBases'], $qp['normbases']);
                    } else {
                        $cnt = 0;
                        foreach ($related[$item['id']]['normbases'] as $items) {
                            $delimiter = (++$cnt != count($related[$item['id']]['normbases'])) ? '; ' : '';
                            echo '<span class="default item">' . $staticDBsContent['normBases'][$items]['title'] . $delimiter . '</span>';
                        }
                    }
                    ?>

                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($related[$item['id']]['smetadocs'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <label><b>Сметная документация:</b></label>
                </div>
                <div class="valueRow matchBlock">

                    <?php
                    if (!empty($qp) && !empty($qp['smetadocs'])) {
                        echo User::printSelect2Items($related[$item['id']]['smetadocs'], $staticDBsContent['smetaDocs'], $qp['smetadocs']);
                    } else {
                        $cnt = 0;
                        foreach ($related[$item['id']]['smetadocs'] as $items) {
                            $delimiter = (++$cnt != count($related[$item['id']]['smetadocs'])) ? '; ' : '';
                            echo '<span class="default item">' . $staticDBsContent['smetaDocs'][$items]['title'] . $delimiter . '</span>';
                        }
                    }
                    ?>

                </div>
            </div>

        <?php endif; ?>

        <?php if (!empty($item['city_id'])) : ?>

            <div class="propRow">
                <div class="labelRow">
                    <label><b>Город:</b></label>
                </div>
                <div class="valueRow matchBlock">
                    <?= ($cityMatch) ? '<span class="match city item">' . $staticDBsContent['cities'][$item['city_id']]['name'] . '</span>' : '<span class="default item">' . $staticDBsContent['cities'][$item['city_id']]['name'] . '</span>' ?>
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<hr/>
