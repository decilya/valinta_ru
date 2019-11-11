<?php

namespace app\controllers;

use app\models\Phone;
use app\models\Rcsc;
use app\models\Request;
use app\models\search\RcscRequestsSearch;
use app\models\ShowUserContactsCounter;
use Faker\Provider\hu_HU\Payment;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\controllers\core\MainController;
use app\models\Auth;
use app\models\Customer;
use app\models\CustomerPhone;
use app\models\Order;
use app\models\OrderFeadbackUser;
use app\models\search\CustomerSearch;
use app\models\Site;
use app\models\Status;
use app\models\User;
use app\models\UserReviewOrder;

/**
 * Class OrderController
 *
 * @package app\controllers
 *
 * @author Ilya <ilya.v87v@gmail.com>
 * @data 26.08.2018
 *
 */
class RcscController extends MainController
{
    /**
     * @return Rcsc|array|null
     * @throws ForbiddenHttpException
     */
    private function checkOnlyForRcsc()
    {
        // Эта песочница где играются ТОЛЬКО ЗАКАЗЧИКИ
        if (Auth::getUserType() !== Auth::TYPE_RCSC) {
            throw new ForbiddenHttpException('Доступ запрещен');
        } else {

            $customerEmail = Yii::$app->user->identity->login;
            /** @var Auth $auth */
            $auth = Auth::find()->where(['login' => $customerEmail])->one();

            /** @var Rcsc $rcsc */
            $rcsc = Rcsc::find()->where(['id' => $auth->rcsc_id])->with('databases')->one();

            return $rcsc;
        }
    }

    /**
     * Страница /rcsc/list/n, где n - id юзера Rcsc, просмотр заявок привязанных к базам добалвенных к юезру
     *
     * @param $id
     * @return mixed
     * @throws ForbiddenHttpException
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 25.09.2019
     */
    public function actionList($id)
    {
        $this->layout = '@app/views/layouts/logform';

        $rcsc = $this->checkOnlyForRcsc();

        if ((int)$rcsc->real_id !== (int)$id) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        if (Yii::$app->request->isAjax) {
            $method = 'renderPartial';
        } else {
            $method = 'render';
        }

        $databasesIds = [];
        foreach ($rcsc->databases as $database) {
            $databasesIds[] = $database->id;
        }

        $params['databasesIds'] = $databasesIds;
        $params['searchId'] = Yii::$app->request->get('searchId');
        $params['start_at'] = Yii::$app->request->get('start_at');
        $params['finish_at'] = Yii::$app->request->get('finish_at');

        $query = Request::find()->groupBy('`requests`.`id`');
        if (!empty($params['databasesIds'])) {
            $query->innerJoin('requests_has_database', '`requests_has_database`.`request_id` = `requests`.`id`');
            $query->andWhere(['in', 'requests_has_database.database_id', $params['databasesIds']]);
        }

        if (!empty($params['searchId'])) {
            $query->andFilterWhere(['requests.id' => (int)$params['searchId']]);
        }

        if ((!empty($params['start_at'])) && (!empty($params['finish_at']))) {
            $startAt = strtotime($params['start_at']);
            $finishAt = strtotime($params['finish_at']);

            $query->andFilterWhere(['>', 'date_created', $startAt]);
            $query->andFilterWhere(['<', 'date_created', $finishAt]);
        }

        $query->groupBy('`requests`.`id`');
        $query->orderBy(['requests.id' => SORT_DESC]);
        $query->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'totalCount' => $query->count(),
                'pageSize' => Yii::$app->params['itemsOnUserIndexPage'],
                'defaultPageSize' => Yii::$app->params['itemsOnUserIndexPage'],
            ],
        ]);

        $results = ($rcsc->databases) ? ((!empty($dataProvider)) ? $dataProvider->getModels() : null) : null;
        $cnt = ($dataProvider->pagination->page) * $dataProvider->pagination->pageSize;

        return $this->{$method}('list', [
            'rcsc' => $rcsc,
            'dataProvider' => $dataProvider,
            'rcscRequestsSearch' => new RcscRequestsSearch(),
            'rcscId' => $id,
            'params' => $params,
            'results' => $results,
            'cnt' => $cnt
        ]);

    }


}