<?php

namespace app\controllers;

use app\models\Auth;
use app\models\Customer;
use app\models\CustomerPhone;
use app\models\forms\FastLoginOrderForm;
use app\models\forms\FastRegOrderForm;
use app\models\forms\LoggedInOrderForm;
use app\models\Order;
use app\models\OrderFeadbackUser;
use app\models\OrderSearch;
use app\models\Site;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\filters\AccessControl;

/**
 * Class OrderController
 * @package app\controllers
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'only' => ['admin-list'],
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['admin-list'],
                        'matchCallback' => function () {
                            if (!isset(Yii::$app->user->identity->is_admin)) {
                                return false;
                            } else {
                                return ((bool)Yii::$app->user->identity->is_admin) ? true : false;
                            }
                        }
                    ],
                ], //
            ],
        ];
    }

    /**
     * @param $action
     * @return bool|Response
     */
    public function beforeAction($action)
    {
        if (Auth::getUserType() == Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        if (Site::checkUserAgent()) {
            $this->layout = 'dummy';
            echo $this->render('dummy');

            return false;
        }

        if (isset(Yii::$app->user->identity->is_admin)) {
            if (Yii::$app->user->identity->is_admin == 1) $this->layout = 'admin';
        }

        return true;
    }

    /**
     * Список всех заказов
     *
     * Реализован не через Search Model из-за специфических треобваний к фильтрации (по сути фильтр на клиенте не фильтр, а сортировка,
     * а так же из-за отсутсвии пагинатора (но Search Model (OrderSearch) есть и используется в админке. см. actionAdminList())
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest && (bool)Yii::$app->user->identity->is_admin) $this->redirect('/user/index');

        $staticDBsContent = Site::prepareStaticDBsContent();

        $msg = Site::checkFlashMessages();

        $positionOrderInResultArr = Yii::$app->request->post('positionOrderInResultArr');
        $professionsFilter = Yii::$app->request->post('professionsFilter');
        $normBasesFilter = Yii::$app->request->post('normBasesFilter');
        $smetaDocsFilter = Yii::$app->request->post('smetaDocsFilter');
        $priceSort = Yii::$app->request->post('priceSort');
        $dataResultsTotal = Yii::$app->request->post('dataResultsTotal');

        if (empty($positionOrderInResultArr)) {
            // грязные хаки (для счетчика)
            $positionOrderInResultArr = -1;
        }

        if (empty($dataResultsTotal)) {
            $dataResultsTotal = 1;
        }

        if (empty($priceSort)) {
            $priceSort = 'desc';
        }

        $qp = [
            'professions' => $professionsFilter,
            'normBases' => $normBasesFilter,
            'smetaDocs' => $smetaDocsFilter
        ];

        $result = Order::find()
            ->where(['published' => 1])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('phones')
            ->orderBy("`updated_at` " . $priceSort)
            ->asArray()
            ->all();

        $countOnAllPublicOrder = count($result);

        $related = [];
        $resultTmp = [];
        $dataArr = [];

        $auth = isset(Yii::$app->user->identity->id) ? Auth::findOne(['id' => Yii::$app->user->identity->id]) : false;

        foreach ($result as $key => &$item) {

            $item['statusFeadBack'] = (isset($auth->user_id)) ? (($auth) ? Site::canUserGetContactThisOrder($item['id'], $auth->user_id, $auth) : null) : null;

            $professionsZ = $smetadocsZ = $normbasesZ = array();

            $itemId = $item['id'];

            $resultTmp[$itemId] = $item;

            foreach ($item['professions'] as $prof) {
                $professionsZ[] = $prof['id'];
            }

            foreach ($item['smetaDocs'] as $smet) {
                $smetadocsZ[] = $smet['id'];
            }

            foreach ($item['normBases'] as $norm) {
                $normbasesZ[] = $norm['id'];
            }

            $related[$itemId] = [
                'professions' => $professionsZ,
                'smetaDocs' => $smetadocsZ,
                'normBases' => $normbasesZ
            ];

            $dataArr[] = $item['updated_at'];
        }

        if ((!empty($professionsFilter)) || (!empty($normBasesFilter)) || (!empty($smetaDocsFilter))) {

            $result = Order::countWeight($qp, $resultTmp, $related);

            $weight = [];
            foreach ($result as $key => $row) {
                $weight[$key] = $row['weight'];
            }

            if ($priceSort == 'desc') {
                array_multisort($weight, SORT_DESC, $dataArr, SORT_DESC, $result);
            } else {
                array_multisort($weight, SORT_DESC, $dataArr, SORT_ASC, $result);
            }
        }

        $positionOrderInResultArr++;
        $result = array_slice($result, ($positionOrderInResultArr), 7);
        $typeOfUser = Auth::getUserType();
        $user = null;

        if ($typeOfUser === Auth::TYPE_USER) {
            $user = User::findOne(['id' => Yii::$app->user->identity->user_id]);
        } elseif ($typeOfUser === Auth::TYPE_CUSTOMER) {
            $user = Customer::findOne(['id' => Yii::$app->user->identity->customer_id]);
        }

        if (Yii::$app->request->isAjax) {

            return $this->renderPartial('/order/blocks/_result-block', [
                'results' => $result,
                'staticDBsContent' => $staticDBsContent,
                'qp' => $qp,
                'countOnAllPublicOrder' => $countOnAllPublicOrder,
                'dataResultsTotal' => $dataResultsTotal,
                'positionOrderInResultArr' => $positionOrderInResultArr,
                'priceSort' => $priceSort,
                'typeOfUser' => $typeOfUser,
                'user' => $user
            ]);

        } else {

            return $this->render('index', [
                'results' => $result,
                'staticDBsContent' => $staticDBsContent,
                'qp' => $qp,
                'priceSort' => $priceSort,
                'countOnAllPublicOrder' => $countOnAllPublicOrder,
                'dataResultsTotal' => $dataResultsTotal,
                'positionOrderInResultArr' => $positionOrderInResultArr,
                'typeOfUser' => $typeOfUser,
                'msg' => $msg,
                'user' => $user
            ]);
        }
    }

    /**
     * Создание заказа
     *
     * @return array|string|\yii\console\Response|Response
     *
     * @author Ilya <ilya.v87v@gmail.com>  a.k.a via
     * @data 19.08.2019
     *
     * @todo переписать по-человечески: сократить длинну раза так в два, убрать дерганье форм, подключть vue.js
     *
     */
    public function actionCreate()
    {
        if (Auth::getUserType() == Auth::TYPE_ADMIN) {
            return $this->redirect('/user/index');
        }

        if (Auth::getUserType() == Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        $customer = null;

        if (Auth::getUserType() == Auth::TYPE_CUSTOMER) {
            $customer = Customer::findOne(['id' => Auth::getUserRealId()]);
        }

        $cloneOrderId = (int)Yii::$app->request->get('cloneOrderId');

        $order = new Order();

        if (Yii::$app->request->post('byAgreement') == 'on') {
            $order->price = 0;
        }

        if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($order);
        }

        $msg = Site::checkFlashMessages();

        $userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->identity->id;
        $isUser = false;

        if ($userId !== 0) {
            if (Auth::getUserType() === Auth::TYPE_USER) {
                $isUser = true;
            }
        }

        if (!$isUser) {

            if ($cloneOrderId !== 0) {

                /** @var Order $cloneOrder */
                $cloneOrder = Order::find()
                    ->where(['id' => $cloneOrderId])
                    ->with('professions')
                    ->with('smetaDocs')
                    ->with('normBases')
                    ->one();

                if (!empty($cloneOrder)) {

                    if (!Yii::$app->user->isGuest) {
                        if ($cloneOrder->auth_id !== Yii::$app->user->identity->id) {
                            Yii::$app->session->setFlash('error', "Невозможно создать новый заказ по шаблону не Вашего заказа.");
                        } else {
                            $order = clone $cloneOrder;

                            $order->id = null;
                            $order->created_at = null;
                            $order->updated_at = null;
                            $order->finished_at = null;
                            $order->closing_reason = null;
                            $order->closing_reason_text = null;
                            $order->user_change_id = null;
                            $order->checked = 0;
                            $order->published = 1;
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "Необходимо авторизоваться, чтобы создать новый заказ по шаблону.");
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Шаблон для создания нового заказа не найден.");
                }
            }

            if (Yii::$app->user->isGuest) {

                $fastRegOrderForm = new FastRegOrderForm();

                if (Yii::$app->request->isAjax && ($fastRegOrderForm->load(Yii::$app->request->post()))) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($fastRegOrderForm);
                }

                if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($order);
                }

            } else {

                /** @var Auth $auth */
                $auth = Auth::find()->where(['id' => Yii::$app->user->identity->id])->one();

                /** @var Customer $customer */
                $customer = Customer::find()
                    ->where(['id' => $auth->customer_id])
                    ->with('customerPhones')
                    ->one();

                $fastRegOrderForm = new LoggedInOrderForm();
                $fastRegOrderForm->login = Yii::$app->user->identity->login;
                $order->auth_id = $customer->real_id;
                $order->fio = $customer->name;
                $order->email = $customer->email;
                $customer->customerPhones = $customer->getCustomerPhones()->all();

                /** @var CustomerPhone[] $phones */
                $phones = $customer->customerPhones;
                $order->setPhonesByCustomer($phones);
            }

            if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($order);
            }

            $timeNow = time();

            $staticDBsContent = Site::prepareStaticDBsContent();

            if (Yii::$app->request->isPost) {
                if ($order->load(Yii::$app->request->post())) {

                    // кол-во дней после истечения которых заказ будет закрыт
                    $dayToFinishNewOrder = Yii::$app->params['dayToFinishNewOrder'];

                    $order->created_at = $timeNow;
                    $order->updated_at = $timeNow;  // Поиск и сортировки будут по полю обновления,
                    // по дате же можно будет узнать реальный возраст заказа,
                    // если такое когда-либо понадобится

                    $order->auth_id = $userId;
                    $order->user_change_id = null;
                    $order->finished_at = strtotime("+$dayToFinishNewOrder day", $timeNow);
                    $order->published = 1;

                    $order->text = strip_tags($order->text);
                    $order->fio = strip_tags($order->fio);
                    $order->name = strip_tags($order->name);

                    if (empty(Order::find()
                        ->where(['name' => $order->name])
                        ->andWhere(['email' => $order->email])
                        ->andWhere(['price' => $order->price])
                        ->andWhere(['text' => $order->text])
                        ->andWhere(['fio' => $order->fio])
                        ->andWhere(['>', 'created_at', time() - 60])
                        ->one())) {

                        if ((Yii::$app->params['switchForRegNewOrder'] == 1) && ($order->save())) {

                            $order->savePhoneNumbers();

                            // формирование писем
                            ////// заказчику
                            $messageForCustomer = $this->renderPartial('//site/messages/_message-order-customer', [
                                'order' => $order,
                                'staticDBsContent' => $staticDBsContent
                            ]);

                            try {
                                Site::sendMessage(
                                    $messageForCustomer,
                                    $order->email,
                                    Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id
                                );
                            } catch (\yii\base\Exception $e) {
                            }

                            /////////////////
                            //// менеджеру
                            /////////////////
                            $messageForManager = $this->renderPartial('//site/messages/_message-order-manager', [
                                'order' => $order,
                                'staticDBsContent' => $staticDBsContent
                            ]);

                            try {
                                Site::sendMessage(
                                    $messageForManager,
                                    Yii::$app->params['mailToManagers'],
                                    Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id
                                );
                            } catch (\yii\base\Exception $e) {
                            }

                            // Перенаправим пользователя на страницу редактирования
                            $str = "<strong>Вы успешно завершили регистрацию заказа.</strong><br><br>
                            Ваш заказ №" . $order->id . " от " . date('d.m.Y', time()) . " размещён 
                            на нашем портале сроком на <strong>30 календарных дней.</strong>
                            Информация о заказе разослана всем подходящим сметчикам, зарегистрированным на портале.";

                            Yii::$app->session->setFlash('successCabinetHeader', $str);

                            return $this->redirect(Url::to(['customer/order-list/']));

                        } else {
                            Yii::$app->session->setFlash('errorCabinetHeader', "Ошибка при попытке сохранения нового заказа.");
                        }
                    } else {
                        return $this->redirect(Url::to(['customer/order-list/']));
                    }
                }
            }
        } else {
            $staticDBsContent = [];
            $fastRegOrderForm = [];
        }

        $order->orderSetRelatedOption();

        return $this->render('create', [
            'model' => $order,
            'staticDBsContent' => $staticDBsContent,
            'fastRegOrderForm' => $fastRegOrderForm,
            'msg' => $msg,
            'isUser' => $isUser,
            'customer' => $customer
        ]);
    }

    public function actionCreateAjax()
    {
        if (Auth::getUserType() === Auth::TYPE_ADMIN) return $this->redirect('/user/index');

        $cloneOrderId = (int)Yii::$app->request->get('cloneOrderId');

        $order = new Order();

        if (Yii::$app->request->post('byAgreement') == 'on') {
            $order->price = 0;
        }

        if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($order);
        }

        $msg = Site::checkFlashMessages();

        $userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->identity->id;
        $isUser = false;

        if ($userId !== 0) {
            if (Auth::getUserType() === Auth::TYPE_USER) {
                $isUser = true;
            }
        }

        if (!$isUser) {

            if ($cloneOrderId !== 0) {

                /** @var Order $cloneOrder */
                $cloneOrder = Order::find()
                    ->where(['id' => $cloneOrderId])
                    ->with('professions')
                    ->with('smetaDocs')
                    ->with('normBases')
                    ->one();

                if (!empty($cloneOrder)) {

                    if (!Yii::$app->user->isGuest) {
                        if ($cloneOrder->auth_id !== Yii::$app->user->identity->id) {
                            Yii::$app->session->setFlash('error', "Невозможно создать новый заказ по шаблону не Вашего заказа.");
                        } else {
                            $order = clone $cloneOrder;

                            $order->id = null;
                            $order->created_at = null;
                            $order->updated_at = null;
                            $order->finished_at = null;
                            $order->closing_reason = null;
                            $order->closing_reason_text = null;
                            $order->user_change_id = null;
                            $order->checked = 0;
                            $order->published = 1;
                        }
                    } else {
                        Yii::$app->session->setFlash('error', "Необходимо авторизоваться, чтобы создать новый заказ по шаблону.");
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Шаблон для создания нового заказа не найден.");
                }
            }

            if (Yii::$app->user->isGuest) {

                $fastRegOrderForm = new FastRegOrderForm();

                if (Yii::$app->request->isAjax && ($fastRegOrderForm->load(Yii::$app->request->post()))) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($fastRegOrderForm);
                }

                if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($order);
                }

            } else {

                /** @var Auth $auth */
                $auth = Auth::find()->where(['id' => Yii::$app->user->identity->id])->one();

                /** @var Customer $customer */
                $customer = Customer::find()
                    ->where(['id' => $auth->customer_id])
                    ->with('customerPhones')
                    ->one();

                $fastRegOrderForm = new LoggedInOrderForm();
                $fastRegOrderForm->login = Yii::$app->user->identity->login;
                $order->auth_id = $auth->id;
                $order->fio = $customer->name;
                $order->email = $customer->email;
                $customer->customerPhones = $customer->getCustomerPhones()->all();

                /** @var CustomerPhone[] $phones */
                $phones = $customer->customerPhones;
                $order->setPhonesByCustomer($phones);
            }

            if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($order);
            }

            $timeNow = time();

            $staticDBsContent = Site::prepareStaticDBsContent();

            if (Yii::$app->request->isPost) {
                if ($order->load(Yii::$app->request->post())) {

                    // кол-во дней после истечения которых заказ будет закрыт
                    $dayToFinishNewOrder = Yii::$app->params['dayToFinishNewOrder'];

                    $order->created_at = $timeNow;
                    $order->updated_at = $timeNow;  // Поиск и сортировки будут по полю обновления,
                    // по дате же можно будет узнать реальный возраст заказа,
                    // если такое когда-либо понадобится

                    $order->auth_id = $userId;
                    $order->user_change_id = null;
                    $order->finished_at = strtotime("+$dayToFinishNewOrder day", $timeNow);
                    $order->published = 1;

                    $order->text = strip_tags($order->text);
                    $order->fio = strip_tags($order->fio);
                    $order->name = strip_tags($order->name);


                    if (empty(Order::find()
                        ->where(['auth_id' => $order->auth_id])
                        ->andWhere(['name' => $order->name])
                        ->andWhere(['email' => $order->email])
                        ->andWhere(['price' => $order->price])
                        ->andWhere(['text' => $order->text])
                        ->andWhere(['email' => $order->email])
                        ->andWhere(['published' => $order->published])
                        ->andWhere(['checked' => $order->checked])
                        ->one())) {

                        if ((Yii::$app->params['switchForRegNewOrder'] == 1) && ($order->save())) {

                            $order->savePhoneNumbers();

                            // формирование писем
                            ////// заказчику
                            $messageForCustomer = $this->renderPartial('//site/messages/_message-order-customer', [
                                'order' => $order,
                                'staticDBsContent' => $staticDBsContent
                            ]);

                            try {
                                Site::sendMessage(
                                    $messageForCustomer,
                                    $order->email,
                                    Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id
                                );
                            } catch (\yii\base\Exception $e) {
                            }

                            /////////////////
                            //// менеджеру
                            /////////////////
                            $messageForManager = $this->renderPartial('//site/messages/_message-order-manager', [
                                'order' => $order,
                                'staticDBsContent' => $staticDBsContent
                            ]);

                            try {
                                Site::sendMessage(
                                    $messageForManager,
                                    Yii::$app->params['mailToManagers'],
                                    Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id
                                );
                            } catch (\yii\base\Exception $e) {
                            }

                            // Перенаправим пользователя на страницу редактирования
                            $str = "<strong>Вы успешно завершили регистрацию заказа.</strong><br><br>
                            Ваш заказ №" . $order->id . " от " . date('d.m.Y', time()) . " размещён 
                            на нашем портале сроком на <strong>30 календарных дней.</strong>
                            Информация о заказе разослана всем подходящим сметчикам, зарегистрированным на портале.";

                            Yii::$app->session->setFlash('successCabinetHeader', $str);

                            return $this->redirect(Url::to(['customer/order-list/']));

                        } else {
                            Yii::$app->session->setFlash('errorCabinetHeader', "Ошибка при попытке сохранения нового заказа.");
                        }


                    } else {
                        //   return $this->redirect(Url::to(['customer/order-list/']));
                    }
                }
            }
        } else {
            $staticDBsContent = [];
            $fastRegOrderForm = [];
        }

        $order->orderSetRelatedOption();

        return $this->render('create', [
            'model' => $order,
            'staticDBsContent' => $staticDBsContent,
            'fastRegOrderForm' => $fastRegOrderForm,
            'msg' => $msg,
            'isUser' => $isUser
        ]);
    }

    /**
     * Страница успешного создания заказа
     *
     * @param $id
     * @return string
     */
    public function actionNewOrderSuccess($id)
    {
        $order = Order::find()->where(['id' => $id])->one();

        return $this->render('success', [
            'order' => $order,
        ]);
    }

    /**
     * Изменение заказа
     *
     * @param $id
     * @param bool $fromLink
     * @return array|string|Response
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $fromLink = false)
    {
        /**
         * @var Order $order
         */
        $order = Order::find()->where(['id' => $id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->one();

        $order->orderSetRelatedOption();

        if (!$order) {
            throw new HttpException(404, 'Не найден указаный заказ');
        }

        if (!$fromLink) {
            if (isset(Yii::$app->user->identity->is_admin)) {
                if (!(bool)Yii::$app->user->identity->is_admin) {
                    throw new HttpException(401, 'У вас нет прав на просмотр данного заказа');
                }
            } else {
                throw new HttpException(401, 'У вас нет прав на просмотр данного заказа');
            }
        }

        if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($order);
        }

        $staticDBsContent = Site::prepareStaticDBsContent();

        if (Yii::$app->request->isPost) {
            if ($order->load(Yii::$app->request->post())) {

                if (Yii::$app->request->post('byAgreement') == 'on') {
                    $order->price = 0;
                }

                // сохраним пользователя, изменившего заказ (пользователь (пока 0) или админ
                $order->user_change_id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;

                $order->text = strip_tags($order->text);
                $order->fio = strip_tags($order->fio);
                $order->name = strip_tags($order->name);

                $oldModel = Order::findOne($order->id);
                $oldModel->fetchPhoneNumbers('order_phones', 'order_id');

                $order->checkDirtyPhoneNumbers($oldModel);

                if ($order->save()) {

                    $order->manageDirtyPhoneNumbers($oldModel, 'order_phones', 'order_id');

                    if ((bool)$order->importantAttributesChanged) {
                        $messageManager = $this->renderPartial('//site/messages/_message-to-manager-order-update', [
                            'order' => $order,
                            'staticDBsContent' => $staticDBsContent
                        ]);

                        try {
                            Site::sendMessage($messageManager, Yii::$app->params['mailToManagers'], "ИПАП - VALINTA.RU - ЗАКАЗ №" . $order->id . " ТРЕБУЕТ ПРОВЕРКИ");
                        } catch (\yii\base\Exception $e) {
                        }

                    }

                    return $this->refresh();
                }
            }
        }

        if (isset(Yii::$app->user->identity->is_admin)) {
            if (Yii::$app->user->identity->is_admin) {
                $this->layout = 'admin';
            }
        }

        $order->fetchPhoneNumbers('order_phones', 'order_id');

        return $this->render('update', [
                'model' => $order,
                'staticDBsContent' => $staticDBsContent,
                // 'fromLink' => $fromLink
            ]
        );
    }

    /**
     * Аяксовый метод для закрытия заказа пользователем
     *
     * @return bool
     */
    public function actionCloseOrderByCustomer()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $orderId = Yii::$app->request->post('orderId');
        $closingReason = Yii::$app->request->post('closingReason');
        $action = Yii::$app->request->post('action');

        if (!$action || empty($action) || $action == '') $action = 'close';

        /**
         * @var Order $order
         */
        $order = Order::find()->where(['id' => $orderId])->one();

        if ($order) {

            if ($action == 'close') {

                if ((int)$order->published == 1) {
                    $order->published = 0;
                }

                $order->closing_reason = $closingReason;
                $order->finished_at = time();

                $order->save(false);

//                if ((isset(Yii::$app->user->identity->is_admin)) && ((Yii::$app->user->identity->is_admin))) {
//                    // сделать что-то если админ
//                } else {
//                    //    Yii::$app->session->setFlash('success', "Ваш заказ №$orderId успешно закрыт!");
//                }
            }

            return true;

        } else {
            return false;
        }
    }

    /**
     * Страница просмотра заказа
     *
     * @param $id
     * @return string
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionView($id)
    {
        /**
         * @var Order $order
         */
        $order = Order::find()->where(['id' => $id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->one();

        if (empty($order)) throw new BadRequestHttpException('Заказ не существует!');
        $order->orderSetRelatedOption();
        $order->fetchPhoneNumbers('order_phones', 'order_id');


        $staticDBsContent = Site::prepareStaticDBsContent();

        $user = null;
        if ((isset(Yii::$app->user->identity->user_id)) && (Yii::$app->user->identity->user_id !== null)) {
            $user = User::findOne(['id' => Yii::$app->user->identity->user_id]);
        }


        return $this->render('view', [
            'model' => $order,
            'staticDBsContent' => $staticDBsContent,
            'months' => Site::MONTHS,
            'user' => $user
        ]);
    }

    /**
     * Список всех заказов в админке
     *
     * @return string
     */
    public function actionAdminList()
    {
        /**
         * @var Order $order
         */

        Url::remember();

        /** Включает панель админа */
        $this->layout = 'admin';

        if (Auth::getUserType() !== Auth::TYPE_ADMIN) {

            //throw new BadRequestHttpException("Доступ закрыт");

            if (Auth::getUserType() === Auth::TYPE_CUSTOMER) {
                Yii::$app->session->setFlash('error', "К сожалению у Вас недостаточно прав для просмотра этой страницы");
                return $this->redirect(['customer/update', 'id' => Auth::getUserRealId()]);
            } else {
                return $this->redirect(['order/index']);
            }
        }

        $searchModel = new OrderSearch();
        $searchModel->dataResultsTotal = 1;

        $searchText = null;
        $searchId = null;
        $searchStatus = null;

        if (Yii::$app->request->isAjax) {
            $method = 'renderPartial';
        } else {
            $method = 'render';
        }

        if (Yii::$app->request->get()) {
            $searchText = Yii::$app->request->get('searchText');
            $searchId = Yii::$app->request->get('searchId');
            $searchStatus = Yii::$app->request->get('searchStatus');

            $searchModel->searchText = $searchText;
            $searchModel->searchId = $searchId;
            $searchModel->searchStatus = $searchStatus;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $results = $dataProvider->getModels();
        $cnt = ($dataProvider->pagination->page) * $dataProvider->pagination->pageSize;

        $staticDBsContent = Site::prepareStaticDBsContent();

        foreach ($results as $item) $item->fetchPhoneNumbers('order_phones', 'order_id');

        return $this->{$method}('adminList', [
            'results' => $results,
            'staticDBsContent' => $staticDBsContent,
            'dataProvider' => $dataProvider,
            'cnt' => $cnt,
            'searchText' => $searchText,
            'searchId' => $searchId,
            'searchStatus' => $searchStatus
        ]);
    }

    /**
     * Метод для подтверждения заказа админом
     *
     * @return bool
     */
    public function actionApprove()
    {
        /**
         * @var Order $order
         */
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $orderId = Yii::$app->request->post('orderId');
        $order = Order::find()->where(['id' => $orderId])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->one();

        if ($order) {
            $order->isNoUpdate = 1;
            $order->checked = 1;
            $order->save(false);

            return true;

        } else {
            return false;
        }

    }

    /**
     * Метод для закрытия заказа АДМИНОМ
     * @return bool
     */
    public function actionClose()
    {
        /**
         * @var Order $order
         */

        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $orderId = Yii::$app->request->post('orderId');
        $reason = Yii::$app->request->post('reason');

        $order = Order::find()->where(['id' => $orderId])->one();

        if ($order) {

            $order->checked = 1;
            $order->closing_reason = Order::CLOSING_REASON_ADMIN;
            $order->closing_reason_text = $reason;
            $order->published = 0;
            $order->finished_at = time();

            if (!empty(Yii::$app->user->identity->id)) {
                $order->user_change_id = Yii::$app->user->identity->id;
            }

            if (isset($reason) && (!empty($reason))) {

                $staticDBsContent = Site::prepareStaticDBsContent();

                // формирование писем
                $messageForCustomer = $this->renderPartial('//site/messages/_message-to-customer-after-close-order-admin', [
                    'order' => $order,
                    'staticDBsContent' => $staticDBsContent
                ]);

                try {
                    Site::sendMessage(
                        $messageForCustomer,
                        $order->email,
                        Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomerOrderCloseFromAdmin'] . $order->id
                    );
                } catch (\yii\base\Exception $e) {
                }

            }

            $order->save(false);

            if (isset($reason) && (!empty($reason))) {

                // Отправляем информационные сообщения на адреса электронной почты Сметчиков, которые откликнулись на заказы,
                // размещенные Заказчиком, см. Задача #20468
                $userReviewOrder = OrderFeadbackUser::find()
                    ->where(['order_id' => $order->id])
                    ->each();

                /** @var OrderFeadbackUser $orderUser */
                foreach ($userReviewOrder as $orderUser) {

                    $userId = $orderUser->user_id;
                    $user = User::findOne(['id' => $userId]);

                    if (!empty($user)) {

                        $messageUser = $this->renderPartial('//customer/messages/_message-user-closed-order', [
                            'user' => $user,
                            'order' => $order
                        ]);

                        try {
                            Site::sendMessage(
                                $messageUser,
                                $user->email,
                                Yii::$app->params['messageSubjects']['mailSubjectUserClosedOrderClosedCustomer'] . $orderUser->order_id
                            );
                        } catch (\yii\base\Exception $e) {
                        }
                    }

                }
            }

            return true;

        } else {
            return false;
        }
    }

    /**
     * Отправка письма клиенту со ссылки на страницу редактирования заказа
     */
    public function actionSendLinkToCustomerFromAdmin()
    {
        /**
         * @var Order $order
         */

        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $orderId = Yii::$app->request->post('orderId');
        $order = Order::find()->where(['id' => $orderId])->one();

        if ($order) {

            if ($order->sendLinkToCustomerFromAdmin($this)) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function actionCheckCustomer()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $login = Yii::$app->request->post('login');

        /** @var Auth $fastAuth */
        $fastAuth = Auth::find()->where(['login' => $login])->one();


        if (!empty($fastAuth)) {
            if ($fastAuth->customer_id != null) {
                /** @var Customer $customer */
                $customer = Customer::find()->where(['email' => $login])->one();

                if (!empty($customer)) {
                    return json_encode(1);
                }
            } elseif ($fastAuth->user_id != null) {
                return json_encode(2);
            }

        }

        return json_encode(false);
    }

    /**
     * get-login-form
     */
    public function actionGetLoginForm()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::to(['order/create']));
        }

        $fastLoginOrderForm = new FastLoginOrderForm();

        return json_encode($this->renderPartial('/order/forms/_fastLoginForm', [
            'fastLoginOrderForm' => $fastLoginOrderForm
        ]));
    }

    /**
     * get-reg-form
     */
    public function actionGetRegForm()
    {
        $fastRegOrderForm = new FastRegOrderForm();

        return json_encode($this->renderAjax('/order/forms/_fastRegForm', [
            'fastRegOrderForm' => $fastRegOrderForm
        ]));
    }

    /**
     * check-password
     */
    public function actionCheckPasswordAndLogin()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $password = Yii::$app->request->post('password');
        $login = Yii::$app->request->post('login');

        /** @var Auth $model */
        $model = new Auth(['scenario' => Auth::SCENARIO_AUTHORIZATION]);
        $model->login = $login;
        $model->password = $password;

        if ($model->login()) {

            $auth = Auth::findOne(Yii::$app->user->identity->id);
            $auth->scenario = Auth::SCENARIO_REGISTER;

            $auth->last_auth = time();

            $auth->save();

            return json_encode(true);
        }

        return json_encode(false);
    }

    /**
     * get-order-for-customer
     */
    public function actionGetOrderForCustomer()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $login = Yii::$app->request->post('login');
        $rawData = Yii::$app->request->post('rawData');

        /**  @var Customer $customer */
        $customer = Customer::find()->where(['email' => $login])->with('customerPhones')->one();

        $order = new Order();
        $staticDBsContent = Site::prepareStaticDBsContent();

        // замена empty
        if ($customer instanceof Customer) {

            $order->fio = $customer->name;
            $order->email = $customer->email;
            $customer->customerPhones = $customer->getCustomerPhones()->all();

            /** @var CustomerPhone[] $phones */
            $phones = $customer->customerPhones;
            $order->setPhonesByCustomer($phones);
        }

        if (isset($rawData['text']) && ($rawData['text'])) {
            $order->text = $rawData['text'];
        }

        if (isset($rawData['name']) && ($rawData['name'])) {
            $order->name = $rawData['name'];
        }

        if (isset($rawData['fio']) && ($rawData['fio'])) {
            $order->fio = $rawData['fio'];
        }

        if (isset($rawData['price']) && ($rawData['price'])) {
            $order->price = $rawData['price'];
        }

        $newPhone = [];
        if (isset($rawData['phone1']) && ($rawData['phone1'])) {
            $newPhone[0] = new CustomerPhone();
            $newPhone[0]->phone = $rawData['phone1'];
            $newPhone[0]->customer_id = $customer->id;
        }

        if (isset($rawData['phone2']) && ($rawData['phone2'])) {
            $newPhone[1] = new CustomerPhone();
            $newPhone[1]->phone = $rawData['phone2'];
            $newPhone[1]->customer_id = $customer->id;
        }

        if (isset($rawData['phone3']) && ($rawData['phone3'])) {
            $newPhone[2] = new CustomerPhone();
            $newPhone[2]->phone = $rawData['phone3'];
            $newPhone[2]->customer_id = $customer->id;
        }

        if (!empty($newPhone)) {
            $order->setPhonesByCustomer($newPhone);
        }

        if (isset($rawData['byAgreement']) && ($rawData['byAgreement'])) {
            $order->byAgreement = $rawData['byAgreement'];
        }

        if (isset($rawData['professions']) && ($rawData['professions'])) {
            $order->professions = $rawData['professions'];
        }

        if (isset($rawData['normBases']) && ($rawData['normBases'])) {
            $order->normBases = $rawData['normBases'];
        }

        if (isset($rawData['smetaDocs']) && ($rawData['smetaDocs'])) {
            $order->smetaDocs = $rawData['smetaDocs'];
        }

        return json_encode($this->renderAjax('forms/_form', [
            'model' => $order,
            'staticDBsContent' => $staticDBsContent,
            'isCreate' => true,
        ]));
    }

    /**
     * Аякс перезагрузка формы заказа
     */
    public function actionResetOrderForm()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $order = new Order();
        $staticDBsContent = Site::prepareStaticDBsContent();
        return json_encode($this->renderAjax('forms/_form', [
            'model' => $order,
            'staticDBsContent' => $staticDBsContent,
            'isCreate' => true,
        ]));
    }

    public function actionRegCustomer()
    {
        // выключим CSRF токен
        $this->enableCsrfValidation = false;

        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->post()) {
            return false;
        }

        $customer = new Customer();
        $customer->scenario = Customer::SCENARIO_REGISTER;
        $customer->password = Yii::$app->request->post('password');
        $customer->rePassword = Yii::$app->request->post('password');
        $customer->name = Yii::$app->request->post('name');
        $customer->email = Yii::$app->request->post('login');
        $customer->created_at = time();
        $phones = Yii::$app->request->post('phones');

        $tmpCustomerPhoneTmp = [];

        foreach ($phones as $phone) {
            if (!empty($phone)) {
                $tmpCustomerPhoneTmp[] = strval($phone);
            }
        }

        $customer->customerPhones = $tmpCustomerPhoneTmp;

        $notUnique = Auth::find()->where(['login' => $customer->email])->one();

        if (empty($notUnique)) {
            if ($customer->save()) {

                $messageManager = $this->renderPartial('/customer/messages/_message-manager-customer-reg', [
                    'customer' => Customer::find()->where(['id' => $customer->id])->with('customerPhones')->one()
                ]);

                try {
                    Site::sendMessage(
                        $messageManager,
                        Yii::$app->params['mailToManagers'],
                        Yii::$app->params['messageSubjects']['mailSubjectCustomerRegistrationManager']
                    );
                } catch (\yii\base\Exception $e) {

                }

                $messageUser = $this->renderPartial('/site/messages/_message-customer-reg', [
                    'customer' => Customer::find()->where(['id' => $customer->id])->with('customerPhones')->one()
                ]);

                try {
                    Site::sendMessage(
                        $messageUser,
                        $customer->email,
                        Yii::$app->params['messageSubjects']['mailSubjectCustomerRegistration']
                    );
                } catch (\yii\base\Exception $e) {
                }

                Yii::$app->user->login(Auth::findOne(['customer_id' => $customer->id]));

                return json_encode($customer->id);

            }
        } // else {
//            // die('$notUnique');
//        }

        return json_encode(false);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     */
    public function actionUpdateCustomerOrder($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login', 'from1' => 'order', 'from2' => 'update-customer-order', 'from_id' => $id]);
        }

        $fromLink = false;

        /** @var Order $order */
        $order = Order::find()->where(['id' => $id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->one();

        $order->orderSetRelatedOption();

        if (!$order || $order->auth_id !== Yii::$app->user->identity->id) {
            throw new HttpException(404, 'Не найден указаный заказ');
        }

        if (Yii::$app->request->isAjax && ($order->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($order);
        }

        $staticDBsContent = Site::prepareStaticDBsContent();

        if (Yii::$app->request->isPost) {
            if ($order->load(Yii::$app->request->post())) {

                if (Yii::$app->request->post('byAgreement') == 'on') {
                    $order->price = 0;
                }

                $order->user_change_id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;

                $order->text = strip_tags($order->text);
                $order->fio = strip_tags($order->fio);
                $order->name = strip_tags($order->name);

                $oldModel = Order::findOne($order->id);
                $oldModel->fetchPhoneNumbers('order_phones', 'order_id');

                $order->checkDirtyPhoneNumbers($oldModel);

                if ($order->save()) {

                    $order->manageDirtyPhoneNumbers($oldModel, 'order_phones', 'order_id');

                    if ((bool)$order->importantAttributesChanged) {
                        $messageManager = $this->renderPartial('//site/messages/_message-to-manager-order-update', [
                            'order' => $order,
                            'staticDBsContent' => $staticDBsContent
                        ]);

                        try {
                            Site::sendMessage(
                                $messageManager,
                                Yii::$app->params['mailToManagers'],
                                "ИПАП - VALINTA.RU - ЗАКАЗ №" . $order->id . " ТРЕБУЕТ ПРОВЕРКИ"
                            );
                        } catch (\yii\base\Exception $e) {
                        }
                    }

                    return $this->refresh();
                }
            }
        }

        if (isset(Yii::$app->user->identity->is_admin)) {
            if (Yii::$app->user->identity->is_admin) {
                $this->layout = 'admin';
            }
        }

        $order->fetchPhoneNumbers('order_phones', 'order_id');
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $order->auth_id])->one();
        $customer = Customer::find()->where(['id' => $auth->customer_id])->one();

        return $this->render('updateCustomerOrder',
            [
                'model' => $order,
                'staticDBsContent' => $staticDBsContent,
                'fromLink' => $fromLink,
                'customer' => $customer
            ]
        );
    }

    /**
     * Аяксовый метод записи отклика сметчика на заказ
     *
     * @return bool
     */
    public function actionUserAnswerOrder()
    {
        if (Yii::$app->user->isGuest) return false;

        if (!Yii::$app->request->isAjax) return false;

        $userId = Yii::$app->request->post('userId');

        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $userId])->one();

        $user = User::findOne(['id' => $auth->user_id]);

        if (empty($user)) return false;

        $userId = $user->id;

        $orderId = Yii::$app->request->post('orderId');

        /** @var Order $order */
        $order = Order::find()->where(['id' => $orderId])->one();

        if (empty($order)) return false;

        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $order->auth_id])->one();
        $customer = Customer::find()->where(['id' => $auth->customer_id])->one();

        $alreadyIs = OrderFeadbackUser::find()
            ->where(['user_id' => $userId])
            ->andWhere(['order_id' => $orderId])
            ->andWhere(['customer_id' => $customer->id])
            ->one();

        if (empty($alreadyIs)) {

            /** @var OrderFeadbackUser $orderFeadbackUser */
            $orderFeadbackUser = new OrderFeadbackUser();
            $orderFeadbackUser->user_id = $userId;
            $orderFeadbackUser->order_id = $orderId;
            $orderFeadbackUser->customer_id = $customer->id;
            $orderFeadbackUser->new = 1;

            if ($orderFeadbackUser->save()) {


                // Письмо счатья


                return json_encode('new');
            }

        } else {

            return json_encode('old');

        }

        return json_encode(false);
    }

}