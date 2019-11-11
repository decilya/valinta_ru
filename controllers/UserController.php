<?php

namespace app\controllers;

use app\controllers\core\MainController;
use app\models\Auth;
use app\models\Customer;
use app\models\Order;
use app\models\Report;
use app\models\Request;
use app\models\Site;
use app\models\Status;
use Yii;
use app\models\User;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Faker;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                        'actions' => ['order-list', 'get-user'],
                        'matchCallback' => function () {
                            return (!Yii::$app->user->isGuest) ? true : false;
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'change-visibility'],
                        'matchCallback' => function () {
                            return ((bool)Yii::$app->user->identity->is_user) ? true : false;
                        }
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return ((bool)Yii::$app->user->identity->is_admin) ? true : false;
                        }
                    ],
                ],
            ],
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
     * Lists all User models.
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $qp = Yii::$app->request->queryParams;

        $arr = User::constructAdminFiltersQuery($qp);
        /** @var ActiveQuery $query */
        $query = $arr['query'];
        $filterParams = $arr['filterParams'];

        $countQuery = clone $query;
        $paginator = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsOnUserIndexPage'],
            'defaultPageSize' => Yii::$app->params['itemsOnUserIndexPage']
        ]);

        $sortUser = Yii::$app->request->get('sort');
        if ($sortUser == null) $sortUser = 0;

        if (($sortUser == null) || ($sortUser == 0)) {
            /** @var ActiveQuery $query */
            $model = $query->offset($paginator->offset)
                ->limit($paginator->limit)
                ->indexBy('id')
                ->orderBy([
                    'status_id' => (
                    new Expression('FIND_IN_SET(users.status_id, "' .
                        Yii::$app->params['status']['pending'] . ',' .
                        Yii::$app->params['status']['rejected'] . ',' .
                        Yii::$app->params['status']['accepted'] . '")')),
                    'date_changed' => SORT_ASC
                ])
                ->all();
        } elseif ($sortUser == 1) {
            $model = $query->offset($paginator->offset)
                ->limit($paginator->limit)
                ->indexBy('id')
                ->innerJoin('auth', '`auth`.`user_id` = `users`.`id`')
                ->orderBy([
                    'auth.id' => SORT_DESC
                ])
                ->all();
        } elseif ($sortUser == 2) {
            $model = $query->offset($paginator->offset)
                ->limit($paginator->limit)
                ->indexBy('id')
                ->innerJoin('auth', '`auth`.`user_id` = `users`.`id`')
                ->orderBy([
                    'auth.id' => SORT_DESC
                ])
                ->all();
        } else {
            throw new Exception('Неправильно задан статус сортировки. Исправьте параметр "sort" в строке адреса!');
        }

        User::determineStatusMessage($model);

        $status = Status::find()->indexBy('id')->all();

        $staticDBsContent = Site::prepareStaticDBsContent();

        if (!empty($model)) {
            $related = User::prepareRelatedArrays(array_keys($model));
        }

        $searchBlock = $this->renderPartial('blocks/_search-block', [
            'filterParams' => $filterParams,
            'sortUser' => $sortUser
        ]);

        foreach ($model as $item) $item->fetchPhoneNumbers('users_phones', 'user_id');

        return $this->render('index', [
            'model' => $model,
            'paginator' => $paginator,
            'status' => $status,
            'staticDBsContent' => $staticDBsContent,
            'related' => (!empty($related)) ? $related : null,
            'searchBlock' => $searchBlock
        ]);
    }

    /**
     * This action renders user update page, processes submitted data and updates DB.
     * Also it processes submitted requests.
     *
     * @param $id
     * @return array|string|Response
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $auth = Auth::find()->where(['id' => Yii::$app->user->identity->id])->one();

        if ((bool)$auth->is_user) {
            $this->layout = '@app/views/layouts/main';
        }

        /** @var Auth $authUser */
        $authUser = Auth::find()->where(['id' => $id])->one();
        $model = User::find()->where(['id' => $authUser->user_id])->one();

        if (((Yii::$app->user->identity->is_admin != 1) && ($auth->id != $id)) || empty($model)) {
           throw new ForbiddenHttpException('You are not allowed to view this page.');
        }

        $model->scenario = User::SCENARIO_UPDATE;

        $model->fillUserWithJunctionTablesData(Yii::$app->params['junctionTablesSetup']);

        $status = Status::find()->indexBy('id')->asArray()->all();

        $msg = Site::checkFlashMessages();

        if (!empty($msg)) {
            if ($msg['key'] === 'msgRequestSuccess' || $msg['key'] === 'msgRequestFail') {
                $msgUpdate = null;
                $msgRequest = $msg;
            } else {
                $msgUpdate = $msg;
                $msgRequest = null;
            }
        }

        $staticDBsContent = Site::prepareStaticDBsContent();

        if (Yii::$app->request->isAjax && ($model->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {

            $oldModel = $this->findModel($id);
            $oldModel->fetchPhoneNumbers('users_phones', 'user_id');

            $model->prepareUserAttributes('update');

            $model->checkDirtyPhoneNumbers($oldModel);

            $model->last_change_by_user = ((bool)$auth->is_user) ? 1 : 0;

            if (!empty($model->getDirtyAttributes(['fio', 'experience', 'price', 'ipap_attestat_id'])) || $model->dirtyPhoneNumbers) {
                if ($auth->is_user) {
                    $model->status_id = Yii::$app->params['status']['pending'];

                    $model->importantAttributesChanged = true;
                }

                // if (!empty($model->getDirtyAttributes(['email']))) {
                $authData = Auth::find()->where(['user_id' => $model->id])->one();

                if ($authData->login != $model->email) {
                    $authData->scenario = Auth::SCENARIO_REGISTER;
                    $authData->login = strip_tags(trim($model->email));
                }
                //  }
            }

            /** @todo смена email */

            $model->compareJunctionTableEntries(Yii::$app->params['junctionTablesSetup']);

            if (empty($authData) && $model->save() || !empty($authData) && $authData->save(false) && $model->save(false)) {
                Yii::$app->session->setFlash('msgUserUpdateSuccess', Yii::$app->params['messages']['msgUserUpdateSuccess']['body']);

                $model->manageDirtyPhoneNumbers($oldModel, 'users_phones', 'user_id');

                if ((bool)$model->importantAttributesChanged) {
                    $messageManager = $this->renderPartial('//site/messages/_message-to-manager-user-update', [
                        'user' => $model,
                        'staticDBsContent' => $staticDBsContent
                    ]);

                    try {
                        Site::sendMessage($messageManager, Yii::$app->params['mailToManagers'], "ИПАП - VALINTA.RU - АНКЕТА №" . $auth->id . " ТРЕБУЕТ ПРОВЕРКИ");
                    } catch (\yii\base\Exception $e) {
                    }
                }

            } else {
                Yii::$app->session->setFlash('msgUserUpdateFail', Yii::$app->params['messages']['msgUserUpdateFail']['body']);
            }

            return $this->refresh();
        }

        $model->fetchPhoneNumbers('users_phones', 'user_id');

        User::determineStatusMessage($model);

        $reports = User::gatherReportsCount($id);

        return $this->render('update', [
            'model' => $model,
            'requestBlock' => (!empty($requestBlock) ? $requestBlock : null),
            'msg' => (!empty($msgUpdate)) ? $msgUpdate : null,
            'staticDBsContent' => $staticDBsContent,
            'status' => $status,
            'is_user' => $auth->is_user,
            'reports' => $reports
        ]);
    }

    /**
     * This action changes user's status to accepted.
     *
     * @param mixed $id Id of user.
     * @param mixed $anchor Id of item which was before or after affected user on user/index page, it is needed to scroll to approximate page position after redirecting back.
     */
    public function actionAcceptUser($id, $anchor)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $id])->one();
        if (!empty($auth)) {
            /** @var User $user */
            $user = User::find()->where(['user_id' => $auth->id])->one();
            $user->scenario = User::SCENARIO_UPDATE;

            $user->status_id = Yii::$app->params['status']['accepted'];
            if (!empty($user->reject_msg)) $user->reject_msg = null;

            if ($user->save(true, ['status_id', 'reject_msg'])) {

                $msg = $this->renderPartial('messages/_message-acceptUser', [
                    'user' => $user
                ]);

                try {
                    // Письмо менежеру
                    /** @todo  проверить и спросить приъодит ли письмо менеджеру  */
                    // Site::sendMessage($msg, Yii::$app->params['mailToManagers'], "ИПАП - VALINTA.RU - АНКЕТА №" . $auth->id . " ПОДТВЕРЖДЕНА");

                    Site::sendMessage($msg, $user->email, "ИПАП - VALINTA.RU - АНКЕТА №" . $auth->id . " ПОДТВЕРЖДЕНА");
                } catch (\yii\base\Exception $e) {
                }
            }

            $tail = User::buildReturnLinkTail($anchor);

            $this->redirect('/user/index' . $tail);
        }
    }

    /**
     * This action changes user's status to rejected.
     *
     * @param mixed $id Id of user.
     * @param mixed $anchor Id of item which was before or after affected user on user/index page, it is needed to scroll to approximate page position after redirecting back.
     */
    public function actionRejectUser($id, $anchor)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $id])->one();

        if (!empty($auth)) {
            /** @var User $user */
            $user = User::find()->where(['user_id' => $auth->id])->one();
            $user->scenario = User::SCENARIO_UPDATE;

            $user->status_id = Yii::$app->params['status']['rejected'];

            if ($user->save(true, ['status_id'])) {

                $msg = $this->renderPartial('messages/_message-rejectUser', [
                    'user' => $user
                ]);

                try {
                    Site::sendMessage($msg, $user->email, Yii::$app->params['messageSubjects']['mailUserRejected']);
                } catch (\yii\base\Exception $e) {
                }
            }
        }

        $tail = User::buildReturnLinkTail($anchor);

        $this->redirect('/user/index' . $tail);

    }

    /**
     * This action saves admin-written reject message to users's row in DB.
     */
    public function actionSaveRejectMsg()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $data = json_decode(file_get_contents('php://input'));

            if (!empty($data->id) && !empty($data->msg)) {
                /** @var Auth $auth */
                $auth = Auth::find()->where(['id' => (int)$data->id])->one();
                /** @var User $user */
                $user = User::find()->where(['user_id' => $auth->user_id])->one();
                $user->scenario = User::SCENARIO_UPDATE;

                $user->reject_msg = strip_tags(trim($data->msg));
                $user->status_id = Yii::$app->params['status']['rejected'];
                $user->is_visible = 0;

                if ($user->save(true, ['reject_msg'])) {
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
        exit;
    }

    /**
     * This action changes is_visible column value in DB and sends corresponding message to user.
     *
     * @param mixed $id Id of user.
     * @throws ForbiddenHttpException
     */
    public function actionChangeVisibility($id)
    {
        //We forbid user to change visibility of other users.
        if ((bool)Yii::$app->user->identity->is_user && ($id != Yii::$app->user->identity->user_id)) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        /** @var User $user */
        $user = User::find()->where(['id' => $id])->one();
        $user->scenario = User::SCENARIO_UPDATE;

        $user->is_visible = ($user->is_visible) ? 0 : 1;

        if ($user->save(true, ['is_visible'])) {

            if ($user->is_visible) {
                Yii::$app->session->setFlash('msgUserChangeVisibilityOnShowSuccess', Yii::$app->params['messages']['msgUserChangeVisibilityOnShowSuccess']['body']);
            } else {
                Yii::$app->session->setFlash('msgUserChangeVisibilityOnHideSuccess', Yii::$app->params['messages']['msgUserChangeVisibilityOnHideSuccess']['body']);
            }

        } else {
            Yii::$app->session->setFlash('msgUserChangeVisibilityFail', Yii::$app->params['messages']['msgUserChangeVisibilityFail']['body']);
        }

        $this->redirect('/user/update/' . $user->real_id);
    }

    /**
     * @param $id
     * @throws \yii\base\Exception
     */
    public function actionSendInstructions($id)
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isGet) {

            $user = User::find()->where(['id' => $id])->one();

            if (!empty($user)) {
                $auth = Auth::find()->where(['user_id' => $id])->one();

                if (!empty($auth)) {

                    $auth->scenario = Auth::SCENARIO_REGISTER;
                    $auth->recovery_token = Yii::$app->getSecurity()->generateRandomString(50);

                    if ($auth->save()) {

                        $messageLinkSent = $this->renderPartial('/site/messages/_message-linkSent', [
                            'user' => $user,
                            'recovery_token' => $auth->recovery_token,
                            'sentByAdmin' => true
                        ]);

                        try {
                            Site::sendMessage($messageLinkSent, $user->email, Yii::$app->params['messageSubjects']['mailRecover']);
                        } catch (\yii\base\Exception $e) {
                        }

                        echo json_encode([
                            'body' => str_replace('ваш e-mail', 'адрес ' . $user->email, Yii::$app->params['messages']['msgRecoverLinkSent']['body']),
                            'status' => Yii::$app->params['messages']['msgRecoverLinkSent']['status']
                        ]);

                        exit;
                    }
                }
            }

            echo json_encode([
                'body' => Yii::$app->params['messages']['msgRecoverLinkSaveFail']['body'],
                'status' => Yii::$app->params['messages']['msgRecoverLinkSaveFail']['status']
            ]);

            exit;
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $id])->one();

        if (empty($auth)) throw new NotFoundHttpException('The requested page does not exist.');

        if (($model = User::findOne(['id' => $auth->user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetUser()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $userRealId = Yii::$app->request->post('authId');

        $auth = Auth::findOne(['id' => $userRealId]);

        if (empty($auth)) return json_encode(false);

        $user = User::findOne(['id' => $auth->user_id]);

        return json_encode([
            'status_id' => $user->status_id,
            'is_visible' => $user->is_visible
        ]);
    }

//    /**
//     *  Генерирует сметчиков, но только если ты админ
//     *
//     * @param null $id
//     * @return bool
//     */
//    public function actionGenUser($id = null)
//    {
//        if (Auth::getUserType() !== Auth::TYPE_ADMIN) {
//            echo "Доступ закрыт!";
//            return false;
//        }
//
//        $faker = Faker\Factory::create();
//
//        if ($id === null) $id = 1;
//
//        for ($i = 0; $i < $id; $i++) {
//
//            $error = 0;
//
//            label:
//
//            $user = new User();
//            $user->email = $faker->email;
//            $user->fio = $faker->name;
//            $user->experience = $faker->text;
//            $user->has_city = mt_rand(1, 500);
//            $user->price = mt_rand(1, 50000);
//            $user->status_id = 2; // подтсвержен
//            $user->is_visible = 1;
//            $user->reject_msg = $faker->text;
//            $user->phone = '+7(999)999-99-99';
//            $user->password_repeat = $user->password = $faker->password;
//
//            $professionsArr = [];
//            for ($j = 0; $j < mt_rand(1, 10); $j++) {
//                $professionsArr[] = mt_rand(1, 38);
//            }
//
//            $user->professions = $professionsArr;
//            $user->user_agreement = 1;
//
//            $user->scenario = User::SCENARIO_REGISTER;
//
//            if (!$user->save()) {
//                $error++;
//                echo "<pre>";
//                print_r($user->errors);
//                echo "<pre>";
//
//                if ($error < 3) goto label;
//            } else {
//                echo "Сметчик " . $user->fio . '(' . $user->email . ') успешно создан<br>';
//            }
//
//        }
//
//        return true;
//    }

}
