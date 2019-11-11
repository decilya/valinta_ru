<?php

namespace app\controllers;

use app\models\Phone;
use app\models\ShowUserContactsCounter;
use Faker\Provider\hu_HU\Payment;
use Yii;
use yii\db\Exception;
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
 */
class CustomerController extends MainController
{

    public function beforeAction($action)
    {
        if (Auth::getUserType() === Auth::TYPE_RCSC) {
            return $this->redirect('/rcsc/list/' . Yii::$app->user->identity->id);
        }

        return parent::beforeAction($action);

    }

    /**
     * @return Customer|array|null
     * @throws ForbiddenHttpException
     */
    private function checkOnlyForCustomerAndGetHim()
    {
        // Эта песочница где играются ТОЛЬКО ЗАКАЗЧИКИ
        if (Auth::getUserType() !== Auth::TYPE_CUSTOMER) {

            if (Auth::getUserType() === Auth::TYPE_GUEST) {
                return $this->redirect('/site/login/');
            }

            throw new ForbiddenHttpException('Доступ запрещен');
        } else {

            $customerEmail = Yii::$app->user->identity->login;

            /** @var Auth $auth */
            $auth = Auth::find()->where(['login' => $customerEmail])->one();
            $customer = Customer::find()->where(['id' => $auth->customer_id])->one();

            return $customer;
        }
    }

    /**
     * Список заказов
     *
     * @return string
     */
    public function actionOrders()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login/');
        }

        $orders = Order::find()->where(['auth_id' => Yii::$app->user->identity->id])->all();

        return $this->render('orders', [
            'customer' => $orders,
        ]);
    }

    /**
     * Регистрация нового заказчика
     *
     * @return Response|array|string
     */
    public function actionCustomerRegistration()
    {
        if (!Yii::$app->user->isGuest) Yii::$app->user->logout();

        /** @var Customer $customer */
        $customer = new Customer();
        $customer->real_id = 0;

        $customer->scenario = Customer::SCENARIO_REGISTER;

        if (Yii::$app->request->isAjax && ($customer->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($customer);
        }

        $customerPostArr = Yii::$app->request->post('Customer');
        $customer->customerPhones = $customerPostArr['customerPhones'];

        if ($customer->load(Yii::$app->request->post())) {

            /** $var Customer $tmpCustomer */
            // защита от 2х кликов
            $tmpCustomer = Customer::find()
                ->where(['>', 'created_at', time() - 60])
                ->innerJoin('auth', '`auth`.`customer_id` = `customer`.`id`')
                ->andWhere(['auth.login' => $customer->email])
                ->one();

            if (empty($tmpCustomer)) {

                if ($customer->validate()) {

                    if (!$customer->save()) {
                        Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');
                    } else {

                        $customer->refresh();

                        $messageManager = $this->renderPartial('messages/_message-manager-customer-reg', [
                            'customer' => $customer,
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
                            'customer' => $customer,
                        ]);

                        try {
                            Site::sendMessage(
                                $messageUser,
                                $customer->email,
                                Yii::$app->params['messageSubjects']['mailSubjectCustomerRegistration']
                            );
                        } catch (\yii\base\Exception $e) {

                        }

                        Yii::$app->session->setFlash('success', 'Вы успешно зарегистрировались на сайте бесплатных объявлений
                     для специалистов по сметному делу «Valinta.ru»!');

                        $customer->refresh();

                        $auth = Auth::findOne(['customer_id' => $customer->id]);
                        Yii::$app->user->login($auth);

                        return $this->redirect(['customer/update/', 'id' => $auth->id]);
                    }
                }
            } else {
                $tmpCustomer->refresh();

                $auth = Auth::findOne(['customer_id' => $tmpCustomer->id]);

                Yii::$app->user->login($auth);
                return $this->redirect(['customer/update/', 'id' => $auth->id]);
            }
        }

        return $this->render('customerRegistration', [
            'customer' => $customer
        ]);
    }

    /**
     * Редактирование пользователя (как самим юзером, так и манагером)
     *
     * @param $id
     * @return array|string|Response
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdate($id)
    {

        /** @var Auth $auth  */
        $auth = Auth::find()->where(['id' => $id])->one();

        /** @var Customer $customer */
        $customer = Customer::find()->where(['id' => $auth->customer_id])->with('customerPhones')->one();

        if (empty($customer)) throw new HttpException(404, 'Нет такого пользователя');

        /** @var Customer scenario */
        $customer->scenario = Customer::SCENARIO_UPDATE;

        if (Yii::$app->request->isAjax && ($customer->load(Yii::$app->request->post()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($customer);
        }

        if (((Auth::getUserType() == Auth::TYPE_CUSTOMER) && (Yii::$app->user->identity->id === $customer->real_id)) ||
            (Auth::getUserType() == Auth::TYPE_ADMIN)) {

            foreach ($customer->relatedRecords['customerPhones'] as $i) {
                /** @var CustomerPhone $i */
                $customer->customerPhones[] = $i->phone;
            }

            $oldCustomerPhones = $customer->customerPhones;

            if (Yii::$app->request->isPost) {

                if ($customer->load(Yii::$app->request->post())) {

                    $newVar = [];

                    $customerPhonesArr = [];
                    if (!empty($customer->customerPhones)) {

                        $customerPhones = CustomerPhone::find()->where(['customer_id' => $customer->id])->all();

                        /** @var CustomerPhone $customerPhone */
                        foreach ($customerPhones as $customerPhone) {
                            $customerPhonesArr[] = $customerPhone->phone;
                            $customerPhone->delete();
                        }
                    }

                    $p = Yii::$app->request->post();
                    if (isset($p['Customer']['customerPhones'])) {
                        $customer->customerPhones = $p['Customer']['customerPhones'];
                    }

                    $resultDiffByPhone = array_diff($customerPhonesArr, $customer->customerPhones);
                    $resultDiffByPhone2 = array_diff($customer->customerPhones, $customerPhonesArr);

                    if ((!empty($resultDiffByPhone)) || (!empty($resultDiffByPhone2))) {
                        $newVar[] = 'phone';
                    }

                    if ($customer->validate()) {

                        if (is_array($customer->customerPhones) && is_array($oldCustomerPhones)) {
                            $result = array_intersect($customer->customerPhones, $oldCustomerPhones);

                            if (!(((count($result) == count($oldCustomerPhones))) && ((count($result) == count($customer->customerPhones))))) {

                                // 1. Менять статус профиля на «Требует проверки»
                                $customer->status_id = Customer::STATUS_REQUIRES_VERIFICATION['val'];
                            }
                        }

                        if (!empty($customer->getDirtyAttributes(['name']))) {
                            $newVar[] = 'name';
                        }

                        if (!empty($customer->getDirtyAttributes(['email']))) {

                            /** @var Auth $oldAuth */
                            $oldAuth = Auth::find()->where(['id' => Yii::$app->user->identity->id])->one();

                            $newVar[] = 'email';
                            $customer->realIdForSave = $oldAuth->id;
                        }

                        if ($customer->save()) {

                            $customer->refresh();

                            $customerSaveMsg = (Auth::getUserType() === Auth::TYPE_CUSTOMER) ? "Ваш профиль успешно обновлён!" : "Заказчик №" . $customer->real_id . " успешно обновлён!";
                            Yii::$app->session->setFlash('success', $customerSaveMsg);

                            if (!empty($newVar)) {

                                $messageManagerUpdateCustomer = $this->renderPartial('/customer/messages/_message-manager-update-customer', [
                                    'customer' => $customer,
                                    'sentByAdmin' => true
                                ]);

                                try {
                                    Site::sendMessage(
                                        $messageManagerUpdateCustomer,
                                        Yii::$app->params['mailToManagers'],
                                        Yii::$app->params['messageSubjects']['mailSubjectCustomerUpdatedManager']
                                    );
                                } catch (\yii\base\Exception $e) {

                                }
                            }
                            return $this->redirect(['customer/update', 'id' => $customer->real_id]);
                        } else {

                            Yii::$app->session->setFlash('msgUserUpdateFail', Yii::$app->params['messages']['msgUserUpdateFail']['body']);
                        }
                    }

                }
            }

        } else {
            if (Auth::getUserType() === Auth::TYPE_GUEST) {
                return $this->redirect(['/site/login']);
            }
            throw new HttpException(401, 'Доступ запрещен. Недостаточно прав для просмотра!');
        }


        return $this->render('update', [
            'customer' => $customer,
        ]);
    }

    /**
     * Список заказчиков
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionAdminCustomersList()
    {
        if (Yii::$app->user->isGuest) return $this->redirect(['/site/login']);

        if (!(bool)Yii::$app->user->identity->is_admin) {
            Yii::$app->session->setFlash('error', "К сожалению у Вас недостаточно прав для просмотра этой страницы");
            return $this->redirect(['site/index']);
        }

        $this->layout = 'admin';
        $sortUser = Yii::$app->request->get('sort');

        $searchModel = new CustomerSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $customers = $dataProvider->getModels();

        /** @var Customer $customer */
        foreach ($customers as &$customer) {
            $customer->phones = $customer->relatedRecords['customerPhones'];
        }

        $statusArr = Status::find()->indexBy('id')->asArray()->all();

        $statusArr[1]['realTitle'] = 'Требует проверки';
        $statusArr[2]['realTitle'] = 'Подтверждён';
        $statusArr[3]['realTitle'] = 'Отклонён';

        $search = [];
        if (Yii::$app->request->isGet) {
            $search['status'] = Yii::$app->request->get('status');
            $search['text'] = Yii::$app->request->get('text');
            $search['id'] = Yii::$app->request->get('id');
        }

        return $this->render('adminList', [
            'customers' => $customers,
            'statusArr' => $statusArr,
            'dataProvider' => $dataProvider,
            'search' => $search,
            'sortUser' => $sortUser
        ]);
    }

    /**
     * Востановление пароля пользователя send-customer-instructions
     *
     * @return bool|false|string
     * @throws \yii\base\Exception
     */
    public function actionSendCustomerInstructions()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->isPost) {
            return false;
        }

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return false;
        }

        $customer = Customer::find()->where(['id' => $id])->one();

        if (!empty($customer)) {
            /** @var Auth $auth */
            $auth = Auth::find()->where(['customer_id' => $id])->one();

            if (!empty($auth)) {

                $auth->scenario = Auth::SCENARIO_REGISTER;
                $auth->recovery_token = Yii::$app->getSecurity()->generateRandomString(50);

                if ($auth->save()) {
                    // подсуним в письмо мульку юзера
                    $user = new User();
                    $user->fio = $customer->name;
                    $user->email = $customer->email;

                    $messageLinkSent = $this->renderPartial('/site/messages/_message-linkSent', [
                        'user' => $user,
                        'recovery_token' => $auth->recovery_token,
                        'sentByAdmin' => true
                    ]);

                    try {
                        Site::sendMessage($messageLinkSent, $user->email, Yii::$app->params['messageSubjects']['mailRecover']);
                    } catch (\yii\base\Exception $e) {
                    }

                    return json_encode([
                        'body' => str_replace('ваш e-mail', 'адрес ' . $user->email, Yii::$app->params['messages']['msgRecoverLinkSent']['body']),
                        'status' => Yii::$app->params['messages']['msgRecoverLinkSent']['status']
                    ]);
                }
            }
        }

        return false;
    }

    /**
     * Подтверждение заказчика
     *
     * @param int $id
     *
     * @return bool
     */
    public function actionAdminCustomerAccept($id)
    {
        // Если это не аякс или не гет, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax && Yii::$app->request->isGet) return false;

        /** @var Customer $customer */
        $customer = Customer::find()->where(['id' => $id])->with('customerPhones')->one();

        if (empty($customer)) return false;

        $customer->scenario = Customer::SCENARIO_UPDATE;
        $customer->status_id = Customer::STATUS_CONFIRMED['val'];

        $customer->reason = '';

        if ($customer->save(false)) {

            // письмо
            $messageUser = $this->renderPartial('messages/_message-customer-accept', [
                'customer' => $customer,
            ]);

            try {
                Site::sendMessage(
                    $messageUser,
                    $customer->email,
                    Yii::$app->params['messageSubjects']['mailSubjectCustomerAccepted']
                );
            } catch (\yii\base\Exception $e) {

            }

            return true;

        } else {
            echo "<pre>";
            print_r($customer->errors);
            echo "</pre>";
        }

        return false;
    }

    /**
     * Отклонение заказчика
     *
     * @param int $id
     * @param int $msg
     *
     * @return bool
     *
     * @author Ilya <ilya.v87v@gmail.com> a.k.a via a.k.a @decilya
     * @data 19.08.2019
     */
    public function actionAdminCustomerReject($id, $msg)
    {
        // Если это не аякс или не гет, то просто дальше не будем обрабатывать скрипт
        if ((!Yii::$app->request->isAjax) && (Yii::$app->request->isGet)) {
            return false;
        }

        /** @var Customer $customer */
        $customer = Customer::findOne(['id' => $id]);

        if (empty($customer)) return false;

        $customer->scenario = Customer::SCENARIO_UPDATE;
        $customer->status_id = Customer::STATUS_REJECTED['val'];
        $customer->reason = $msg;

        if ($customer->save(true, ['status_id', 'reason'])) {

            //$userReviewOrder
            $messageUser = $this->renderPartial('messages/_message-customer-reject', [
                'customer' => $customer
            ]);

            try {
                Site::sendMessage(
                    $messageUser,
                    $customer->email,
                    Yii::$app->params['messageSubjects']['mailSubjectCustomerReject']
                );
            } catch (\yii\base\Exception $e) {

            }

            // Связанные с профилем Заказчика заказы должны быть автоматически закрыты.
            // Причина закрытия заказа: Профиль заказчика отклонён.
            $orders = Order::find()
                ->where(['auth_id' => $customer->real_id])
                ->andWhere(['closing_reason' => null])
                ->all();

            /** @var Order $order */
            foreach ($orders as $order) {
                $order->closing_reason = Order::CLOSING_REASON_CLOSED_ORDER;
                $order->closing_reason_text = 'Профиль заказчика отклонён';
                $order->published = 0;

                // Отправляем информационные сообщения на адреса электронной почты Сметчиков, которые откликнулись на заказы,
                // размещенные Заказчиком, см. Задача #20468
                $userReviewOrder = OrderFeadbackUser::find()
                    ->where(['order_id' => $order->id])
                    ->all();

                /** @var OrderFeadbackUser $orderUser */
                foreach ($userReviewOrder as $orderUser) {

                    $userId = $orderUser->user_id;
                    $user = User::findOne(['id' => $userId]);

                    if (!empty($user)) {

                        $messageUser = $this->renderPartial('messages/_message-user-closed-order', [
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

                $order->finished_at = time();

                if (!$order->save(false)) return false;
            }

            return true;
        }

        return false;
    }

    /**
     * order-list
     *
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionOrderList()
    {
        if (Auth::getUserType() == Auth::TYPE_GUEST) return $this->redirect(['site/login']);

        $customer = $this->checkOnlyForCustomerAndGetHim();
        $authId = Yii::$app->user->identity->id;

        /** @var int $goToItem */
        $goToItem = (int)Yii::$app->request->get('goToItem');

        if (!Yii::$app->request->isAjax) {

            $allOrdersCount = Order::find()
                ->where(['auth_id' => $authId])
                ->andWhere(['published' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->all();

            $count = 1;

            if (!empty($goToItem)) {
                $i = 0;
                /** @var Order $itemOrder */
                foreach ($allOrdersCount as $itemOrder) {
                    $i++;
                    if ($i % 7 == 0) $count++;
                    if ($itemOrder['id'] == $goToItem) break;
                }
            }

            $orders = Order::find()
                ->where(['auth_id' => $authId])
                ->orderBy(['id' => SORT_DESC])
                ->andWhere(['published' => 1])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->with('users')
                ->with('userFeadback')
                ->limit(7 * $count)
                ->all();


            return $this->render('orderList', [
                'orders' => $orders,
                'customer' => $customer,
                'allOrdersCount' => count($allOrdersCount),
                'goToItem' => $goToItem
            ]);

        } else {

            $lastOrderId = Yii::$app->request->post('lastOrderId');

            $orders = Order::find()
                ->where(['auth_id' => $authId])
                ->andWhere(['published' => 1])
                ->andWhere('id<:id', [':id' => $lastOrderId])
                ->orderBy(['id' => SORT_DESC])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->with('userFeadback')
                ->limit(7)
                ->all();

            return $this->renderPartial('blocks/_orderItems', [
                'orders' => $orders
            ]);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionCustomerOrderFeedback($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        } elseif (Auth::getUserType() !== Auth::TYPE_CUSTOMER) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        /** @var Order $order */
        $order = Order::find()
            ->where(['id' => $id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('userFeadback')
            ->one();

        /** @var Auth $auth */
        $auth = Auth::findOne(['id' => Yii::$app->user->identity->id]);

        $goToItem = Yii::$app->request->get('user');

        if (($auth->customer_id == null) ||
            ($auth->id != $order->auth_id)) throw new ForbiddenHttpException('Доступ запрещен');

        $customer = Customer::findOne(['id' => $auth->customer_id]);

        if (!Yii::$app->request->isAjax) {

            $countOrderFeadbackUser = OrderFeadbackUser::find()
                ->where(['order_id' => $id])
                ->andWhere(['customer_id' => $auth->customer_id])
                ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                ->andWhere(['users.status_id' => 2])
                ->andWhere(['users.is_visible' => 1])
                ->count();

            $orderFeadbackUser = OrderFeadbackUser::find()
                ->where(['order_id' => $id])
                ->andWhere(['customer_id' => $auth->customer_id])
                ->orderBy(['created_at' => SORT_ASC])
                ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                ->andWhere(['users.status_id' => 2])
                ->andWhere(['users.is_visible' => 1])
                ->limit(7)
                ->all();


            return $this->render('orderFeedBack', [
                'order' => $order,
                'customer' => $customer,
                'orderFeadbackUser' => $orderFeadbackUser,
                'countOrderFeadbackUser' => $countOrderFeadbackUser,
                'goToItem' => $goToItem
            ]);

        } else {

            $lastFeadbackId = Yii::$app->request->get('lastFeadbackId');

            $orderFeadbackUser = OrderFeadbackUser::find()
                ->where(['order_id' => $id])
                ->andWhere('id<:id', [':id' => $lastFeadbackId])
                ->andWhere(['customer_id' => $auth->customer_id])
                ->orderBy(['created_at' => SORT_ASC])
                ->limit(7)
                ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                ->andWhere(['users.status_id' => 2])
                ->andWhere(['users.is_visible' => 1])
                ->all();

            return $this->renderPartial('blocks/_userFeadbackItem', [
                'orderFeadbackUser' => $orderFeadbackUser
            ]);
        }
    }

    public function actionOrderArchiveList()
    {
        /** @var Customer $customer */
        $customer = $this->checkOnlyForCustomerAndGetHim();

        $authId = Yii::$app->user->identity->id;

        if (!Yii::$app->request->isAjax) {

            $allOrdersCount = Order::find()
                ->where(['auth_id' => $authId])
                ->andWhere(['published' => 0])
                ->orderBy(['finished_at' => SORT_DESC])
                ->all();

            $orders = Order::find()
                ->where(['auth_id' => $customer->real_id])
                ->andWhere(['published' => 0])
                ->orderBy(['finished_at' => SORT_DESC])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->with('users')
                ->with('userFeadback')
                ->limit(7)
                ->all();

            return $this->render('orderArchiveList', [
                'orders' => $orders,
                'customer' => $customer,
                'allOrdersInArchiveCount' => count($allOrdersCount),
                'count' => count($orders)
            ]);

        } else {

            $lastOrderId = Yii::$app->request->post('lastOrderId');
            $countOrdersLasts = Yii::$app->request->post('countOrdersLasts');
            // $countOrders = Yii::$app->request->post('countOrders');
            $lastDataFinish = Yii::$app->request->post('lastDataFinish');

            $orders = Order::find()
                ->where(['auth_id' => $authId])
                ->andWhere(['published' => 0])
                ->andWhere('finished_at<:finished_at', [':finished_at' => $lastDataFinish])
                ->orderBy(['finished_at' => SORT_DESC])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->with('userFeadback')
                ->limit((int)$countOrdersLasts)
                ->all();

            return $this->renderPartial('blocks/_orderArchiveItems', [
                'orders' => $orders,
                'count' => count($orders)
            ]);

        }

    }

    /**
     * Увеличение счетика просмотров сметичка пользователем
     *
     * @return bool|false|string
     */
    public function actionIncCounter()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->isPost) {
            return false;
        }

        $customerRealId = (int)Yii::$app->request->post('customer_real_id');
        $userId = (int)Yii::$app->request->post('user_id');

        if (($customerRealId) && ($userId)) {

            /** @var Auth $auth */
            $auth = Auth::find()->where(['id' => $customerRealId])->one();

            /** @var Customer $customer */
            $customer = Customer::findOne(['id' => $auth->customer_id]);

            // Если сметчик не откликался на заказы Заказчика то увеличим счетик
            $userCount = OrderFeadbackUser::find()
                ->where(['user_id' => $userId])
                ->andWhere(['customer_id' => $customer->id])
                ->count();

            if ($userCount == 0) {
                if (ShowUserContactsCounter::incCounter($customer->id, $userId)) {
                    return json_encode(true);
                }
            }
        }

        return json_encode(false);
    }

    /**
     * Аяксовая проверка можно ли покаывать Заказчику контакты Сметчика
     *
     * @return bool|false|string
     */
    public function actionCheckShowLimit()
    {
        // Если это не аякс, то просто дальше не будем обрабатывать скрипт
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!Yii::$app->request->isPost) {
            return false;
        }

        $result = [
            'status' => false,
            'text' => '',
            'counter' => 0
        ];

        $customerAuthId = (int)Yii::$app->request->post('customer_real_id');
        /** @var Auth $customerAuth */
        $customerAuth = Auth::find()->where(['id' => $customerAuthId])->one();

        $userId = (int)Yii::$app->request->post('userId');

        if (($customerAuthId != 0) && ($customerAuth->customer_id != null) && ($userId != 0)) {

            $result['counter'] = ShowUserContactsCounter::getCustomerCounterForDayByAuthId($customerAuthId);

            // Если сметчик откликался на заказы Заказчика и эти заказы опубликованы, то покажем все равно
            $userCount = OrderFeadbackUser::find()
                ->where(['user_id' => $userId])
                ->andWhere(['customer_id' => $customerAuth->customer_id])
                ->innerJoin('order', 'order.id = order_feadback_user.order_id')
                ->andWhere(['order.published' => 1])
                ->count();

            //Если заказчик  уже просматривал контакт сметичка, то тоже разрешим ему
            $createdAt = time() - 86400;
            $customerShowCounter = ShowUserContactsCounter::find()
                ->where(['customer_id' => $customerAuth->customer_id])
                ->andWhere(['user_id' => $userId])
                ->andWhere(['>', 'created_at', $createdAt])
                ->count();

            if (($customerShowCounter > 0) || ($userCount > 0) || ($result['counter'] < (int)Yii::$app->params['limitShowUserContactsCounter'])) {
                $result['status'] = true;
            } else {

                $result['text'] = 'Просмотр контактов будет доступен через ' .
                    (int)(ShowUserContactsCounter::residualTime() / 3600) . 'ч. ' .
                    (int)((ShowUserContactsCounter::residualTime() % 3600) / 60) . " мин.";
            }
        }

        return json_encode($result);
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionRecruitment($id)
    {
        /** @var Order $order */
        $order = Order::find()
            ->where(['id' => $id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('users')
            ->with('userFeadback')
            ->one();

        if (((Auth::getUserType() !== Auth::TYPE_ADMIN && $order->auth_id !== Yii::$app->user->identity->id))) {
            throw new ForbiddenHttpException('нет доступа!');
        }

        $userCount = User::find()
            ->where(['status_id' => Yii::$app->params['status']['accepted']])
            ->andWhere(['is_visible' => 1])
            ->count();

        $sortParams = [
            'filter' => Yii::$app->request->get('filter'),
            'direction' => Yii::$app->request->get('direction'),
        ];

        $customer = $this->checkOnlyForCustomerAndGetHim();

        // берем все отклики сметичков на заказы ДАННОГО ЗАКАЗЧИКА (для вывода (или нет) контактов сметчика)
        /** @var OrderFeadbackUser $orderFeadbackUserForThisCustomerArr */
        $orderFeadbackUserForThisCustomerArrTmp = OrderFeadbackUser::find()
            ->select('user_id')
            ->where(['customer_id' => $customer->id])
            ->andWhere(['order_id' => $order->id])
            ->innerJoin('users', 'order_feadback_user.user_id = users.id')
            ->andWhere(['users.status_id' => Yii::$app->params['status']['accepted']])
            ->andWhere(['users.is_visible' => 1])
            ->orderBy(['users.date_changed' => SORT_ASC])
            ->groupBy(['user_id'])
//            ->with('order')
//            ->with('user')
            ->asArray()
            ->all();

        /// а теперь будет жуткий гавнокод, пацаны, не бейте, лучше обоссыте
        $orderFeadbackUserForThisCustomerArr = [];
        foreach ($orderFeadbackUserForThisCustomerArrTmp as $item) {
            $orderFeadbackUserForThisCustomerArr[] = $item['user_id'];
        }

        if (!Yii::$app->request->isAjax) {

            $lastUserId = Yii::$app->request->get('lastUserId');
            $countItem = Yii::$app->request->get('countItem');

            if (($sortParams['filter'] == 'date') && ($sortParams['direction'] == 'asc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_ASC])
                    ->limit(7)
                    ->all();

            } elseif (($sortParams['filter'] == 'date') && ($sortParams['direction'] == 'desc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_DESC])
                    ->limit(7)
                    ->all();

            } elseif (($sortParams['filter'] == 'price') && ($sortParams['direction'] == 'asc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['price' => SORT_ASC])
                    ->limit(7)
                    ->all();

            } elseif (($sortParams['filter'] == 'price') && ($sortParams['direction'] == 'desc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['price' => SORT_DESC])
                    ->limit(7)
                    ->all();

            } else {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_DESC])
                    ->limit(7)
                    ->all();

            }

            return $this->render('recruitment', [
                'customer' => $customer,
                'order' => $order,
                'users' => $users,
                'countUsers' => $userCount,
                'sortParams' => $sortParams,
                'orderFeadbackUserForThisCustomerArr' => $orderFeadbackUserForThisCustomerArr
            ]);

        } else { /// если херакс, то вот это делай:

            $lastUserId = Yii::$app->request->get('lastUserId');
            $countItem = Yii::$app->request->get('countItem');

            $tmpCount = $userCount - $countItem;

            if ($tmpCount > 7) $tmpCount = 7;

            if (($sortParams['filter'] == 'date') && ($sortParams['direction'] == 'asc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_ASC])
                    ->all();

                $usersTmp = [];
                $i = 0;
                $countTmp = 0;
                foreach ($users as $user) {
                    $countTmp++;
                    if ($countTmp > $countItem) {
                        $usersTmp[] = $user;
                        $i++;
                        if ($i == $tmpCount) break;
                    }
                }

                $users = $usersTmp;

            } elseif (($sortParams['filter'] == 'date') && ($sortParams['direction'] == 'desc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_DESC])
                    ->all();

                $usersTmp = [];
                $i = 0;
                $countTmp = 0;
                foreach ($users as $user) {
                    $countTmp++;
                    if ($countTmp > $countItem) {
                        $usersTmp[] = $user;
                        $i++;
                        if ($i == $tmpCount) break;
                    }
                }

                $users = $usersTmp;

            } elseif (($sortParams['filter'] == 'price') && ($sortParams['direction'] == 'asc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['price' => SORT_ASC])
                    ->all();

                $usersTmp = [];
                $i = 0;
                $countTmp = 0;
                foreach ($users as $user) {
                    $countTmp++;
                    if ($countTmp > $countItem) {
                        $usersTmp[] = $user;
                        $i++;
                        if ($i == $tmpCount) break;
                    }
                }

                $users = $usersTmp;

            } elseif (($sortParams['filter'] == 'price') && ($sortParams['direction'] == 'desc')) {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['price' => SORT_DESC])
                    ->all();

                $usersTmp = [];
                $i = 0;
                $countTmp = 0;
                foreach ($users as $user) {
                    $countTmp++;
                    if ($countTmp > $countItem) {
                        $usersTmp[] = $user;
                        $i++;
                        if ($i == $tmpCount) break;
                    }
                }

                $users = $usersTmp;

            } else {

                $users = User::find()
                    ->where(['status_id' => Yii::$app->params['status']['accepted']])
                    ->andWhere(['is_visible' => 1])
                    ->orderBy(['date_changed' => SORT_DESC])
                    ->all();

                $usersTmp = [];
                $i = 0;
                $countTmp = 0;
                foreach ($users as $user) {
                    $countTmp++;
                    if ($countTmp > $countItem) {
                        $usersTmp[] = $user;
                        $i++;
                        if ($i == $tmpCount) break;
                    }
                }

                $users = $usersTmp;
            }

            return $this->renderPartial('blocks/_recruitmentItems', [
                'order' => $order,
                'users' => $users,
                'sortParams' => $sortParams,
                'orderFeadbackUserForThisCustomerArr' => $orderFeadbackUserForThisCustomerArr
            ]);

        }
    }


}