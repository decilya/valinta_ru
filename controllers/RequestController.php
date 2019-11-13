<?php

namespace app\controllers;

use app\models\Auth;
use app\models\Customer;
use app\models\Database;
use app\models\Order;
use app\models\Profession;
use app\models\Site;
use app\models\User;
use Yii;
use app\models\Request;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends Controller
{
    public $layout = 'admin';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

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
        Site::clearFilterSessionVars();

        if (Auth::getUserType() === Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    /**
     * Lists all Request models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $qp = Yii::$app->request->queryParams;

        $arr = User::constructAdminFiltersQuery($qp, 'Request');

        $query = $arr['query'];
        $filterParams = $arr['filterParams'];

        $countQuery = clone $query;
        $paginator = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsOnRequestIndexPage'],
            'defaultPageSize' => Yii::$app->params['itemsOnRequestIndexPage']
        ]);

        $model = ArrayHelper::toArray($query->offset($paginator->offset)
            ->limit($paginator->limit)
          //  ->indexBy('id')
            ->orderBy('date_created DESC')
            ->all());

        $searchBlock = $this->renderPartial('/user/blocks/_search-block', [
            'filterParams' => $filterParams,
            'requestIndex' => true
        ]);

        return $this->render('index', [
            'model' => $model,
            'searchBlock' => $searchBlock,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Displays a single Request model.
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Request model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Request();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Request model.
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();


        return $this->redirect(['index']);
    }

    /**
     * Confirms request with given id.
     *
     * @param integer $id
     */
    public function actionConfirm($id)
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $request = Request::findOne($id);

            if (!empty($request)) {
                $request->status_value = 2;

                $request->comment = 'Заявка обработана ' .
                    date('d.m.Y', time()) . ' в ' .
                    date('H:i:s', time()) . '; ' .
                    Html::encode(strip_tags(trim($request->comment)));

                if ($request->save(true, ['status_value', 'comment'])) {
                    echo Html::encode(strip_tags(trim($request->comment)));

                    exit;
                }

                echo 0;
            }

            exit;
        }
    }

    public function actionCommentSave($id)
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $data = json_decode(file_get_contents('php://input'));

            if (isset($data)) {
                $request = Request::findOne($id);
                $request->comment = strip_tags(trim($data));

                if ($request->save(true, ['comment'])) {
                    echo 1;

                    exit;
                }

                echo 0;
            }

            exit;
        }
    }

    public function actionCommentCancel($id)
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $request = Request::findOne($id);

            if (!empty($request)) {
                echo Html::encode(strip_tags(trim($request->comment)));
            }

            exit;
        }
    }

    /**
     * Finds the Request model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Request the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Подтвердить все заказы
     *
     * php yii app/approve-all-orders
     */
    public function actionApproveAllOrders()
    {
        $orders = Order::find()->where(['published' => 0])->with('professionsNorm')->with('professions')->all();

        /** @var Order $order */
        foreach ($orders as $order) {
            $professions = $order->getRelatedTitleFrom('professions');
            $order->professions = $professions;
            $order->checked = 1;
            $order->published = 1;
            if (!$order->save()) {
                echo "<pre>";
                print_r($order->errors);
                echo "</pre>";
            }

        }

        $orders = Order::find()->where(['checked' => 0])->with('professionsNorm')->with('professions')->all();

        /** @var Order $order */
        foreach ($orders as $order) {
            $professions = $order->getRelatedTitleFrom('professions');
            $order->professions = $professions;
            $order->checked = 1;
            $order->published = 1;
            if (!$order->save()) {
                echo "<pre>";
                print_r($order->errors);
                echo "</pre>";
            }

        }

    }


    /**
     * Подтвердить всех пользователей и сделать всех видимыми
     *
     * php yii app/approve-all-users
     */
    public function actionApproveAllUsers()
    {
        $users = User::find()->where(['not', 'status_id' => 2])->all();
        /** @var User $order */
        foreach ($users as $user) {
            $user->scenario = User::SCENARIO_UPDATE;
            $user->status_id = 2;
            if (!$user->save()) {
                echo "<pre>";
                print_r($user->errors);
                echo "</pre>";
            }
        }

        $users = User::find()->where(['is_visible' => 0])->all();
        /** @var User $order */
        foreach ($users as $user) {
            $user->scenario = User::SCENARIO_UPDATE;
            $user->is_visible = 1;
            if (!$user->save()) {
                echo "<pre>";
                print_r($user->errors);
                echo "</pre>";
            }
        }
    }


    /**
     * Подтвердить всех закачиков
     *
     * php yii app/approve-all-users
     */
    public function actionApproveAllCustomer()
    {
        $customers = Customer::find()->where(['not', 'status_id' => 2])->all();
        /** @var User $order */
        foreach ($customers as $customer) {
            $customer->status_id = 2;
            if (!$customer->save(false)) {
                echo "<pre>";
                print_r($customer->errors);
                echo "</pre>";
            }
        }

    }

    /**
     * Подтвердить все
     *
     * http://via.yii2-valinta.dsite/request/approve-all
     *
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function actionApproveAll()
    {
        if (Auth::getUserType() === Auth::TYPE_ADMIN) {

            //$this->actionApproveAllOrders();
            $this->actionApproveAllUsers();
            $this->actionApproveAllCustomer();


            echo "Все что мог заэпрувил и сделал видимым!<br>";
            return true;
        }

        throw new ForbiddenHttpException('нет доступа');

    }

}
