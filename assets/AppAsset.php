<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'libs/placeholder/placeholder.js',
        'js/hoverTopMenus.js',
        'libs/tooltip/regFormTooltip.js',

        '/js/easing.js',
        '/js/jquery.ui.totop.min.js',
        // 'libs/snow/snow.js'

        '/js/extraPhoneNumbers.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\BootboxAsset',
        'app\assets\MaskAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

}
