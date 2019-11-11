<?php

namespace app\controllers;

use app\models\Rcsc;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use app\models\Request;
use app\models\User;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\controllers\core\MainController;
use app\models\Auth;
use app\models\Customer;
use app\models\Report;
use app\models\Site;
use app\models\Database;

use Faker;

class SiteController extends MainController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //TODO Must enable this on production.
//                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {

        if (Auth::getUserType() === Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        if (parent::beforeAction($action)) {
            //    if (!Yii::$app->user->isGuest && (bool)Yii::$app->user->identity->is_admin && !Yii::$app->request->isAjax) $this->redirect('/user/index');

            return true;
        }

        return false;
    }

    /**
     * This action renders login page or, if logged in, redirects to other page, depending on identity role.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            if ((bool)Yii::$app->user->identity->is_user) {
                $this->redirect('/user/update/' . Yii::$app->user->identity->user_id);
            }
        }

        $model = new Auth(['scenario' => Auth::SCENARIO_AUTHORIZATION]);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            /** @var  Auth $auth */
            $auth = Auth::findOne(Yii::$app->user->identity->id);
            $auth->scenario = Auth::SCENARIO_AUTHORIZATION;
            $auth->last_auth = time();

            $auth->save();

            if ($auth->is_user === 1) {

                $from1 = Yii::$app->request->get('from1');
                $from2 = Yii::$app->request->get('from2');
                $fromId = Yii::$app->request->get('from_id');

                if ((!empty($from1)) && (!empty($from2))) {

                    if (!empty($fromId)) {
                        return $this->redirect(['/' . $from1 . '/' . $from2 . '/', 'id' => $fromId]);
                    }

                    return $this->redirect(['/' . $from1 . '/' . $from2 . '/']);
                }

                if ($auth->user_id !== null) {
                    return $this->redirect('/user/update/' . $auth->id);
                } elseif ($auth->customer_id !== null) {
                    return $this->redirect('/customer/update/' . $auth->id);
                } elseif ($auth->rcsc_id !== null) {

                    /** @var Rcsc $rcscId */
                    $rcscId = Rcsc::find()->where(['id' => $auth->rcsc_id])->one();

                    if ($rcscId->status_id != Rcsc::STATUS_REJECTED['val']) {
                        $this->layout = '@app/views/layouts/logform';
                        return $this->redirect('/rcsc/list/' . $auth->id);
                    } else {
                        Yii::$app->user->logout();
                        Yii::$app->session->setFlash('error', 'К сожалению, пользователь заблокирован');
                        $this->layout = '@app/views/layouts/logform';
                        return $this->render('login', [
                            'model' => $model,
                        ]);
                    }

                }
            } else {
                return $this->redirect('/user/index');
            }
        }

        $this->layout = '@app/views/layouts/logform';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     *This action renders password recover form page.
     * If submitted login is in DB, sends e-mail with one-time password reset link.
     */
    public function actionRecover()
    {
        $this->layout = '@app/views/layouts/logform';

        $model = new Auth(['scenario' => Auth::SCENARIO_RECOVER_PASS]);

        $msg = Site::checkFlashMessages();

        if (Yii::$app->request->get("email")) {
            $model->login = trim(Yii::$app->request->get("email"));
        }

        //AjaxValidation
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (Yii::$app->request->isPost) {

            //when user requests password change
            if ($model->load(Yii::$app->request->post())) {

                /** @var Auth $auth */
                $auth = Auth::find()->where(['login' => $model->login])->one();
                $auth->scenario = Auth::SCENARIO_REGISTER;
                $auth->recovery_token = Yii::$app->getSecurity()->generateRandomString(50);

                if ($auth->save()) {

                    $user = User::findOne($auth->user_id);
                    /** @var Customer $customer */
                    $customer = Customer::findOne($auth->customer_id);
                    /** @var Rcsc $rcsc */
                    $rcsc = Rcsc::findOne([$auth->rcsc_id]);

                    if (!empty($user)) {
                        Yii::$app->session->setFlash('msgRecoverLinkSent', Yii::$app->params['messages']['msgRecoverLinkSent']['body']);

                        $messageLinkSent = $this->renderPartial('messages/_message-linkSent', [
                            'user' => $user,
                            'recovery_token' => $auth->recovery_token,
                            'sentByAdmin' => false
                        ]);

                        try {
                            Site::sendMessage($messageLinkSent, $model->login, Yii::$app->params['messageSubjects']['mailRecover']);
                        } catch (\yii\base\Exception $e) {
                        }
                    } elseif (!empty($customer)) {

                        $user = new User();
                        $user->fio = $customer->name;
                        $user->email = $customer->email;

                        Yii::$app->session->setFlash('msgRecoverLinkSent', Yii::$app->params['messages']['msgRecoverLinkSent']['body']);

                        $messageLinkSent = $this->renderPartial('messages/_message-linkSent', [
                            'user' => $user,
                            'recovery_token' => $auth->recovery_token,
                            'sentByAdmin' => false
                        ]);

                        try {
                            Site::sendMessage($messageLinkSent, $model->login, Yii::$app->params['messageSubjects']['mailRecover']);
                        } catch (\yii\base\Exception $e) {
                        }

                    } elseif (!empty($rcsc)) {

                        $user = new User();
                        $user->fio = $rcsc->name;
                        $user->email = $rcsc->email;

                        Yii::$app->session->setFlash('msgRecoverLinkSent', Yii::$app->params['messages']['msgRecoverLinkSent']['body']);

                        $messageLinkSent = $this->renderPartial('messages/_message-linkSent', [
                            'user' => $user,
                            'recovery_token' => $auth->recovery_token,
                            'sentByAdmin' => false
                        ]);

                        try {
                            Site::sendMessage($messageLinkSent, $model->login, Yii::$app->params['messageSubjects']['mailRecover']);
                        } catch (\yii\base\Exception $e) {
                        }
                    } else {
                        Yii::$app->session->setFlash('msgRecoverLinkSaveFail', Yii::$app->params['messages']['msgRecoverLinkSaveFail']['body']);
                    }
                }

                $this->refresh();
            }
        }


        return $this->render('recover', [
            'model' => $model,
            'msg' => (!empty($msg)) ? $msg : null,
        ]);

    }

    /**
     *This action renders password change form.
     * If certain conditions passed, changes user password in DB.
     */
    public function actionChangePass()
    {
        if (strpos(Yii::$app->request->getHeaders()['accept'], 'text/html') === false) return true;

        $this->layout = '@app/views/layouts/logform';

        $model = new Auth(['scenario' => Auth::SCENARIO_CHANGE_PASS]);

        $msg = Site::checkFlashMessages();

        $recovery_token = (!empty(Yii::$app->request->get('token'))) ? Yii::$app->request->get('token') : null;

        //error, if user enters /change-pass without recovery token
        if (empty($recovery_token) && !Yii::$app->session->has('recovery_token') && empty($msg)) {
            Yii::$app->session->removeAll();
            Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
            return $this->refresh();
        }

        //user comes in with recovery token in url
        if (!empty($recovery_token)) {

            /** @var Auth $checkToken */
            $checkToken = Auth::find()->where(['recovery_token' => $recovery_token])->one();

            //check recovery token in db
            if (!empty($checkToken)) {
                $checkToken->scenario = Auth::SCENARIO_REGISTER;

                $token = Yii::$app->getSecurity()->generateRandomString(50);

                $checkToken->recovery_token = $token;

                //change recovery token in db and reload page without recovery token in url
                if ($checkToken->save()) {
                    Yii::$app->session->removeAll();
                    Yii::$app->session->set('recovery_token', $token);

                    $this->redirect('/change-pass');

                    //error saving new recovery token
                } else {
                    Yii::$app->session->removeAll();
                    Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
                    $this->redirect('/change-pass');
                }

                //error, if user comes with recovery token that doesn't exist in db
            } else {
                Yii::$app->session->removeAll();
                Yii::$app->session->set('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
                return $this->redirect('/change-pass');
            }
        }

        //when user changes password
        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->session->has('recovery_token')) {
                /** @var Auth $auth */
                $auth = Auth::find()->where(['recovery_token' => Yii::$app->session->get('recovery_token')])->one();

                if (!empty($auth)) {
                    $auth->scenario = Auth::SCENARIO_REGISTER;
                    $auth->password = Yii::$app->getSecurity()->generatePasswordHash($model->pass_change);
                    $auth->recovery_token = null;

                    if ($auth->save(false)) {

                        //logs out user, if he's logged in.
                        if (!Yii::$app->user->isGuest) Yii::$app->user->logout();

                        if ($auth->user_id != null) {

                            $user = User::findOne($auth->user_id);

                            if ($user) {
                                Yii::$app->session->removeAll();
                                Yii::$app->session->setFlash('msgRecoverPasswordSent', Yii::$app->params['messages']['msgRecoverPasswordSent']['body']);

                                $messagePasswordSent = $this->renderPartial('messages/_message-passwordSent', [
                                    'user' => $user,
                                    'auth' => $auth
                                ]);

                                try {
                                    Site::sendMessage($messagePasswordSent, $auth->login, Yii::$app->params['messageSubjects']['mailRecover']);
                                } catch (\yii\base\Exception $e) {
                                }
                            }

                        } elseif ($auth->customer_id != null) {

                            $customer = Customer::findOne($auth->customer_id);

                            if ($customer) {

                                $user = new User();
                                $user->fio = $customer->name;
                                $user->email = $customer->email;

                                Yii::$app->session->removeAll();
                                Yii::$app->session->setFlash('msgRecoverPasswordSent', Yii::$app->params['messages']['msgRecoverPasswordSent']['body']);

                                $messagePasswordSent = $this->renderPartial('messages/_message-passwordSent', [
                                    'user' => $user,
                                    'auth' => $auth
                                ]);

                                try {
                                    Site::sendMessage($messagePasswordSent, $auth->login, Yii::$app->params['messageSubjects']['mailRecover']);
                                } catch (\yii\base\Exception $e) {
                                }
                            }
                        } elseif ($auth->rcsc_id != null) {

                            $rcsc = Rcsc::findOne($auth->rcsc_id);

                            if ($rcsc) {

                                $user = new User();
                                $user->fio = $rcsc->name;
                                $user->email = $rcsc->email;

                                Yii::$app->session->removeAll();
                                Yii::$app->session->setFlash('msgRecoverPasswordSent', Yii::$app->params['messages']['msgRecoverPasswordSent']['body']);

                                $messagePasswordSent = $this->renderPartial('messages/_message-passwordSent', [
                                    'user' => $user,
                                    'auth' => $auth
                                ]);

                                try {
                                    Site::sendMessage($messagePasswordSent, $auth->login, Yii::$app->params['messageSubjects']['mailRecover']);
                                } catch (\yii\base\Exception $e) {
                                }
                            }
                        } else {
                            Yii::$app->session->removeAll();
                            Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
                        }

                    } else {
                        Yii::$app->session->removeAll();
                        Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
                    }

                } else {
                    Yii::$app->session->removeAll();
                    Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
                }

            } else {
                Yii::$app->session->removeAll();
                Yii::$app->session->setFlash('msgChangePassFail', Yii::$app->params['messages']['msgChangePassFail']['body']);
            }

            return $this->refresh();
        }

        return $this->render('change-pass', [
            'model' => $model,
            'msg' => (!empty($msg)) ? $msg : null,
        ]);
    }

    /**
     * This action logs out user.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('login');
    }

    /**
     * This action renders main page of application, processes search filters parameters and returns corresponding results.
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest && (bool)Yii::$app->user->identity->is_admin && !Yii::$app->request->isAjax) $this->redirect('/user/index');

        $qp = Yii::$app->request->queryParams;
        Site::removeUnnecessaryGetParameters($qp);
        $staticDBsContent = Site::prepareStaticDBsContent();

        $sortParams = [
            'filter' => (!empty($qp['sortFilter'])) ? $qp['sortFilter'] : Yii::$app->params['defaultSorting']['filter'],
            'direction' => (!empty($qp['sortDirection'])) ? $qp['sortDirection'] : Yii::$app->params['defaultSorting']['direction']
        ];

        //constructing query that fetches all items that are visible and accepted.
        $query = User::constructQuery();

        $result = $query->indexBy('id')->asArray()->all();

        $countQuery = (int)$query->count();

        $keysQueryResult = [];

        if (!empty($result)) {
            //sort $result array to correct order
            $keysQueryResult = User::processKeysQueryResult($result, $qp, $staticDBsContent, $countQuery, $sortParams);

            $sum = (!empty($qp['showfrom'])) ? ((int)$qp['showfrom'] + (int)Yii::$app->params['searchResultsDefaultLimit']) : Yii::$app->params['searchResultsDefaultLimit'];
            $idsCurrent = implode(',', array_slice($keysQueryResult['ids'], 0, $sum, true));

            //constructing final query that fetches required items in correct order.
            $query = User::constructQuery($sum, 0, $idsCurrent);

            $results = $query->indexBy('id')->asArray()->all();

            $qp = Site::massExplode(['professions', 'normbases', 'smetadocs'], $qp);
        }

        $method = (!Yii::$app->request->isAjax) ? 'render' : 'renderPartial';


        return $this->{$method}('_index', [
            'cityIdArr' => (!empty($keysQueryResult)) ? $keysQueryResult['cityIdArr'] : null,
            'staticDBsContent' => $staticDBsContent,
            'results' => (!empty($results)) ? $results : null,
            'related' => (!empty($related)) ? $keysQueryResult['related'] : Yii::$app->session->get('related'),
            'qp' => $qp,
            'resultsTotal' => (int)$countQuery,
            'sortParams' => $sortParams,
            'matchesPercentArr' => (!empty($keysQueryResult['matchesPercentArr'])) ? $keysQueryResult['matchesPercentArr'] : null,
            'user' => false
        ]);

    }

    /**
     * This action loads additional items to main page.
     */
    public function actionMoreResults()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $data = json_decode(file_get_contents('php://input'));

            if (!empty($data->start) && !empty($data->query)) {

                $keys = Yii::$app->session->get('keys');
                $related = Yii::$app->session->get('related');
                $staticDBsContent = Yii::$app->session->get('staticDBsContent');
                $matchesPercentArr = Yii::$app->session->get('matchesPercentArr');

                if (!empty($keys) && !empty($related) && !empty($staticDBsContent) && !empty($matchesPercentArr)) {

                    $qp = Site::parseQueryString($data->query);

                    $qp = Site::massExplode(['professions', 'normbases', 'smetadocs'], $qp);

                    $query = User::constructQuery(Yii::$app->params['searchResultsDefaultLimit'], $data->start, $keys);
                    $result = $query->indexBy('id')->asArray()->all();

                    $response = '';

                    $cnt = $data->start;

                    foreach ($result as $item) {
                        $cnt++;

                        $response .= $this->renderAjax('blocks/_item-block', [
                            'item' => $item,
                            'staticDBsContent' => $staticDBsContent,
                            'related' => $related,
                            'qp' => $qp,
                            'matchesPercentArr' => $matchesPercentArr,
                            'isLoadMore' => true,
                            'cnt' => $cnt,
                            'months' => Site::MONTHS
                        ]);

                    }

                    echo json_encode([
                        'count' => count($result),
                        'html' => $response,
                        'error' => false
                    ]);

                } else {

                    echo json_encode([
                        'error' => true
                    ]);
                }
            }
            exit;
        }
    }

    /**
     * Статус заказчика
     *
     * get-customer-status
     * @return mixed
     */
    public function actionGetCustomerStatus()
    {
        $post = (Yii::$app->request->post());
        $authId = $post['authId'];

        if ($authId) {
            /** @var Auth $auth */
            $auth = Auth::find()->where(['id' => $authId])->one();

            $customerId = $auth->customer_id;
            $customer = Customer::findOne(['id' => $customerId]);

            return json_decode($customer->status_id);
        }

        return json_decode(null);
    }

    /**
     *Sяends back user e-mail and phone number, it also registers click to use it in statistics' purposes.
     */
    public function actionGetContacts()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $data = json_decode(file_get_contents('php://input'));

            if (!empty($data->id)) {

                $user = User::find()->where([
                    'id' => $data->id
                ])->with('phones')->one();

                if (!empty($user)) {

                    $sessionReportWriteArr = Yii::$app->session->get('sessionReportWriteArr');

                    if (empty($sessionReportWriteArr) || !empty($sessionReportWriteArr) && (array_search($data->id, $sessionReportWriteArr) === false)) {

                        $time = time();

                        $report = new Report([
                            'user_id' => $data->id,
                            'date' => date('Y-m-d H:i:s', $time),
                            'ip' => Yii::$app->request->getUserIP(),
                            'day_index' => date('z', $time),
                            'week_index' => date('W', $time),
                            'month_index' => date('n', $time),
                            'year' => date('Y', $time),
                        ]);

                        if ($report->save()) {
                            if (!empty($sessionReportWriteArr)) {
                                $arr = $sessionReportWriteArr;
                            } else {
                                $arr = [];
                            }

                            $arr[$data->id] = $data->id;
                            Yii::$app->session->set('sessionReportWriteArr', $arr);
                        }
                    }

                    $phoneArr = [];

                    foreach ($user->getRelatedRecords()['phones'] as $phone) {
                        $phoneArr[] = $phone->number;
                    }

                    echo json_encode([
                        'phone' => Html::encode(strip_tags(trim(implode(', ', $phoneArr)))),
                        'email' => "<a href='mailto:" . Html::encode(strip_tags(trim($user->email))) . "'>" . Html::encode(strip_tags(trim($user->email))) . "</a>"
                    ]);
                }
            }
            exit;
        }
    }

    /**
     * This action renders registration page and processes submitted data.
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest && Auth::getUserType() === Auth::TYPE_USER) {
            $this->redirect('/user/update/' . Yii::$app->user->identity->id);
        }

        $request = new Request();
        $user = new User(['scenario' => User::SCENARIO_REGISTER]);

        $msg = Site::checkFlashMessages();

        $staticDBsContent = Site::prepareStaticDBsContent();

        if (Yii::$app->request->isAjax && ($user->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post())) {

            $user->prepareUserAttributes('register');

            if ($user->save()) {

                $user->savePhoneNumbers();

                $user->fillJunctionTables(Yii::$app->params['junctionTablesSetup']);

                // $auth = Auth::createAuthData($user);

                //if ($auth->save()) {
                Yii::$app->session->setFlash('msgRegistrationSuccess', Yii::$app->params['messages']['msgRegistrationSuccess']['body']);

                $user->refresh();

                $messageUser = $this->renderPartial('messages/_message-user', [
                    'user' => $user,
                ]);

                $messageManager = $this->renderPartial('messages/_message-manager', [
                    'user' => $user,
                    'staticDBsContent' => $staticDBsContent
                ]);

                Yii::$app->user->login(Auth::findOne(['login' => $user->email]));

                try {
                    Site::sendMessage($messageManager, Yii::$app->params['mailToManagers'], Yii::$app->params['messageSubjects']['mailSubjectManager']);
                } catch (\yii\base\Exception $e) {
                }

                try {
                    Site::sendMessage($messageUser, $user->email, Yii::$app->params['messageSubjects']['mailSubjectClient']);
                } catch (\yii\base\Exception $e) {
                }

                return Yii::$app->response->redirect(Url::to(['order/index']));

            } else {
                Yii::$app->session->setFlash('msgRegistrationFail', Yii::$app->params['messages']['msgRegistrationFail']['body']);
            }

            return $this->redirect('register');
        }

        return $this->render('_register', [
            'request' => $request,
            'user' => $user,
            'staticDBsContent' => $staticDBsContent,
            'msg' => (!empty($msg)) ? $msg : null
        ]);
    }

    /**
     * This action processes submitted requests.
     */
    public function actionRequest()
    {
        $request = new Request();
        $request->access_days = 1;
        $request->desired_date = date('d.m.Y', time());

        $databasesTmpForList = Database::find()->orderBy('name')->orderBy('id')->all();

        /** @var User $model */
        $model = (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user) ? User::find()
            ->where(['id' => Yii::$app->user->identity->user_id])
            ->with('phones')
            ->one() : null;

        if (is_null($model)) {

            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user) {
                /** @var Auth $auth */
                $auth = Auth::find()->where(['id' => Yii::$app->user->identity->id])->one();
                $model = Customer::find()->where(['real_id' => $auth->customer_id])->with('realCustomerPhones')->one();
            } else {
                $model = null;
            }

            if (!is_null($model)) {
                if ($model) {
                    $modelTmp = new User();
                    $modelTmp->fio = $model->name;
                    $modelTmp->email = $model->email;
                    $modelTmp->phone = isset($model->realCustomerPhones[0]) ? $model->realCustomerPhones[0]->phone : null;

                    $model = $modelTmp;
                }
            }

        } else {
            $phones = $model->getRelatedRecords('phones');

            if (!empty($phones)) {
                $model->phone = $phones['phones'][0]->number;
            }
        }

        $msg = Site::checkFlashMessages();

        if (Yii::$app->request->isAjax && ($request->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($request);
        }

        if ($request->load(Yii::$app->request->post())) {

            $request->date_created = time();

            if ($request->databasesTmp) {
                $request->cost = self::calcDbById($request->databasesTmp, $request->access_days);
            }

            if ($request->save()) {
                Yii::$app->session->setFlash('msgRequestSuccess', Yii::$app->params['messages']['msgRequestSuccess']['body']);

                $request->refresh();
                /** @var Request $request */
                $request = Request::find()->where(['id' => $request->id])->with('databases')->one();

                $messageUser = $this->renderPartial('messages/_message-requestSentToUser', [
                    'request' => $request
                ]);

                $messageManager = $this->renderPartial('messages/_message-requestSentToManager', [
                    'request' => $request
                ]);

                try {
                    Site::sendMessage($messageUser, $request->email, $request->requestMessageSubjWithSuffix(Yii::$app->params['messageSubjects']['mailRequestSent'], $request->id));
                } catch (\yii\base\Exception $e) {
                }

                try {
                    Site::sendMessage($messageManager, Yii::$app->params['mailToManagers'], $request->requestMessageSubjWithSuffix(Yii::$app->params['messageSubjects']['mailRequestSent'], $request->id));
                } catch (\yii\base\Exception $e) {
                }

            } else {
                Yii::$app->session->setFlash('msgRequestFail', Yii::$app->params['messages']['msgRequestFail']['body']);
            }

            return $this->refresh();
        }

        return $this->render('request', [
            'request' => $request,
            'msg' => $msg,
            'model' => $model,
            'databasesTmp' => $databasesTmpForList
        ]);
    }

    public function actionContent($alias)
    {
        if (!empty($alias)) {
            return $this->render('_content', [
                'alias' => $alias
            ]);
        }

        return false;
    }

    public function actionAddPhoneNumber()
    {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {

            $post = Yii::$app->request->post();

            return $this->renderAjax('blocks/_phone-field', [
                'fieldsCount' => $post['fieldsCount'],
                'modelName' => $post['modelName']
            ]);
        }

        return false;
    }

    /**
     * @return string
     */
    public function actionRegistration()
    {
        return $this->render('mainRegistrationPage');
    }


    /**
     * @return bool|string
     */
    public function actionGetAjaxTopMenu()
    {
        if (!Yii::$app->request->isAjax) return false;

        return $this->renderAjax(
            'get-ajax-top-menu'
        );
    }


    /**
     * @param array $listDb
     * @param int $days
     * @return bool|float|int
     */
    public static function calcDb($listDb, $days)
    {
        if ($days == null) $days = 0;
        if (is_array($listDb)) {
            $cost = 0;
            foreach ($listDb as $item) {

                $r = preg_split("/ - [0-9]+р\.$/", $item);

                $nameS = $r[0];

                /** @var Database $db */
                $db = Database::find()->where(['name' => $nameS])->one();
                $cost += $db->cost;
            }

            return (int)$cost * (int)$days;
        }

        return false;
    }


    /**
     * @param array $listDbId
     * @param int $days
     * @return bool|float|int
     */
    public static function calcDbById($listDbId, $days)
    {
        if ($days == null) $days = 0;
        if (is_array($listDbId)) {

            $cost = 0;
            foreach ($listDbId as $item) {

                /** @var Database $db */
                $db = Database::find()->where(['id' => $item])->one();
                $cost += $db->cost;
            }

            return (int)$cost * (int)$days;
        }

        return false;
    }


    /**
     * @return bool|false|string
     */
    public function actionAjaxCalcDb()
    {
        $listDb = Yii::$app->request->post('listDb');
        $days = Yii::$app->request->post('days');

        return json_encode(self::calcDb($listDb, $days));
    }

}
