<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\base\View;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MaskAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];

    public $js = [
		'libs/mask/jquery.inputmask.js',
		'libs/mask/jquery.inputmask.bundle.js',
		'libs/mask/jquery.bind-first.js',
		'libs/mask/jquery.inputmask-multi.js',
		'js/mask.js',
        'js/maskOld.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',

    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
