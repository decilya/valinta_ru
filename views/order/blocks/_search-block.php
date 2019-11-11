<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

?>

<div class="search-block">

    <div class="filtersHolder">

        <div class="filtersRow">
            <div class="labelHolder">
                <div class="tableDiv">
                    <label class="control-label">Профессиональная область</label>
                </div>
            </div>
            <div class="filterHolder">
                <?= Select2::widget([
                    'language' => 'ru',
                    'name' => 'select2-filterProfessions',
                    'id' => 'order-professions',
                    'value' => !empty($qp['professions']) ? $qp['professions'] : '',
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'multiple' => true,
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['professions'], 'id', 'title'),
                    'showToggleAll' => false,
                    'toggleAllSettings' => [
                        'selectLabel' => 'Выбрать всё',
                        'unselectLabel' => 'Убрать всё'
                    ]

                ]) ?>
            </div>
        </div>

        <div class="filtersRow">
            <div class="labelHolder">
                <div class="tableDiv">
                    <label class="control-label">Нормативные базы</label>
                </div>
            </div>
            <div class="filterHolder">
                <?= Select2::widget([
                    'language' => 'ru',
                    'name' => 'select2-filterNormBases',
                    'id' => 'order-normbases',

                    'value' => !empty($qp['normbases']) ? $qp['normbases'] : '',
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'multiple' => true,
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['normBases'], 'id', 'title'),
                    'showToggleAll' => false,
                    'toggleAllSettings' => [
                        'selectLabel' => 'Выбрать всё',
                        'unselectLabel' => 'Убрать всё'
                    ]
                ]) ?>
            </div>
        </div>

        <div class="filtersRow">
            <div class="labelHolder">
                <div class="tableDiv">
                    <label class="control-label">Сметная документация</label>
                </div>
            </div>
            <div class="filterHolder">
                <?= Select2::widget([
                    'language' => 'ru',
                    'name' => 'select2-filterSmetaDocs',
                    'id' => 'order-smetadocs',

                    'value' => !empty($qp['smetadocs']) ? $qp['smetadocs'] : '',
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'multiple' => true,
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['smetaDocs'], 'id', 'title'),
                    'showToggleAll' => false,
                    'toggleAllSettings' => [
                        'selectLabel' => 'Выбрать всё',
                        'unselectLabel' => 'Убрать всё'
                    ]
                ]) ?>
            </div>
        </div>
        <a style="color: #ffffff" id="dropFilters" href="/order/index">Сбросить фильтр</a>
    </div>


</div>

