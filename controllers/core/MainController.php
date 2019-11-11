<?php

namespace app\controllers\core;

use app\models\Auth;
use app\models\Site;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Class OrderController
 * @package app\controllers
 */
class MainController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $oldie = Site::checkUserAgent();

        if ($oldie) {
            $this->layout = 'dummy';
            echo $this->render('dummy');

            return false;
        }

        if (isset(Yii::$app->user->identity->is_admin)) {
            if ((bool)Yii::$app->user->identity->is_admin) {
                $this->layout = 'admin';
            }
        }

        return true;
    }


}