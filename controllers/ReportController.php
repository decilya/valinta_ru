<?php

namespace app\controllers;

use app\models\Auth;
use app\models\Report;
use app\models\Site;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ReportController extends Controller
{
    public $layout = 'admin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),

                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],

                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            if ((bool)Yii::$app->user->identity->is_admin) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    ],
                ]

            ]
        ];
    }

    public function beforeAction($action)
    {

        if (Auth::getUserType() === Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    public function actionAll()
    {
        if (Yii::$app->params['report']['defaultReportRange'] !== 'custom') {
            $range = Report::determineRangeDates(Yii::$app->params['report']['defaultReportRange']);


        } else {
            $range = [
                'dateStart' => strtotime(Yii::$app->params['report']['customDateStart']),
                'dateEnd' => strtotime(Yii::$app->params['report']['customDateEnd'])
            ];
        }

        $reports = Report::prepareReportAll($range['dateStart'], $range['dateEnd'], Yii::$app->params['report']['defaultDetailLevel']);

        return $this->render('all', [
            'reports' => $reports
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionUser($id)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $id])->one();

        /** @var User $user */
        $user = User::find()->where(['id' => $auth->user_id])->one();

        if (!empty($user)) {

            if (Yii::$app->params['report']['defaultReportRange'] !== 'custom') {
                $range = Report::determineRangeDates(Yii::$app->params['report']['defaultReportRange']);
            } else {
                $range = [
                    'dateStart' => strtotime(Yii::$app->params['report']['customDateStart']),
                    'dateEnd' => strtotime(Yii::$app->params['report']['customDateEnd'])
                ];
            }

            $reports = Report::prepareReportAll($range['dateStart'], $range['dateEnd'], Yii::$app->params['report']['defaultDetailLevel'], $user->id);

            return $this->render('user', [
                'reports' => $reports,
                'user' => $user
            ]);
        } else {
            throw new ForbiddenHttpException('Доступ запрещен!');
        }

    }

    public function actionChangeRange()
    {
        $date = json_decode(file_get_contents('php://input'));

        if (!empty($date)) {

            if ($date->range !== 'custom') {
                $range = Report::determineRangeDates($date->range);
            } else {
                $range = [
                    'dateStart' => strtotime($date->dateStart),
                    'dateEnd' => strtotime($date->dateEnd)
                ];
            }

            $reports = Report::prepareReportAll($range['dateStart'], $range['dateEnd'], $date->detailLevel, $date->userId);

            echo json_encode([
                'reports' => $reports,
            ]);

        }

        exit;

    }
}