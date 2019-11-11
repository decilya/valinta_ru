<?php

namespace app\commands;

use app\models\Auth;
use app\models\Customer;
use app\models\CustomerPhone;
use app\models\OrderFeadbackUser;
use app\models\Report;
use app\models\User;
use Seld\CliPrompt\CliPrompt;
use Yii;
use yii\console\Controller;
use app\models\Order;
use app\models\Site;
use yii\db\Connection;
use app\models\OrderUser;

class AppController extends Controller
{
    /** Код красного цвета */
    const COLOR_ERROR = 31;

    /** Код зеленого цвета */
    const COLOR_SUCCESS = 32;

    private function getDsnAttribute($dsn, $name = "dbname")
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    private function printColorStr($str, $code)
    {
        $str = $str . "\n";
        echo "\n";
        $code = array($code);
        echo "\033[" . implode(';', $code) . 'm' . $str . "\033[0m";
    }

    /**
     * Метод создает БД с названием указанным в файле конфига config/db.php
     */
    public function actionCreateDb()
    {
        $nameDb = $this->getDsnAttribute(Yii::$app->getDb()->dsn);
        $username = Yii::$app->getDb()->username;
        $password = Yii::$app->getDb()->password;
        $host = $this->getDsnAttribute(Yii::$app->getDb()->dsn, 'host');
        $mysqli = mysqli_connect($host, $username, $password);

        if (mysqli_connect_errno($mysqli)) {
            $this->printColorStr("Не удалось подключиться к MySQL: " . mysqli_connect_error(), self::COLOR_ERROR);
        }

        $res = mysqli_query($mysqli, "CREATE database " . $nameDb . " CHARACTER SET utf8 COLLATE utf8_general_ci;");

        if ($res) {
            $this->printColorStr("База данных $nameDb успешно создана!", self::COLOR_SUCCESS);
        } else {
            $this->printColorStr('Неудалось создать базу данных ' . $nameDb . '. Возможно, что такая БД уже создана в системе или неправильно заполнен файл config/db.php', self::COLOR_ERROR);
        }

        echo "\n";
    }

    /**
     *
     *  Добавить в систему админа, если его нет. В качестве параметра метод принимает email
     * суперпользователя; при вызове метода без парметра админ будет создан с
     * почтой via@wizardforum.ru. Пример вызова: php yii app/add-admin test@test.com
     *
     * @param string|null $email
     * @throws \yii\base\Exception
     */
    public function actionAddAdmin(string $email = null)
    {
        $model = Auth::find()->where(['login' => 'admin'])->one();

        if (empty($model)) {

            /** @var Auth $user */
            $user = new Auth();
            $user->scenario = Auth::SCENARIO_REGISTER;
            ($email != null) ? ($user->login = $email) : ($user->login = 'admin');

            $user->password =  Yii::$app->getSecurity()->generatePasswordHash('1qwe2qaz');
            $user->is_admin = 1;
            $user->is_user = 0;

            if ($user->save()) {
                $this->printColorStr("Поздравляем, суперпользователь успешно создан в системе! Теперь Вы можете авторизоваться используя логин/пароль: admin/1qwe2qaz\n", self::COLOR_SUCCESS);
            } else {
                echo "<pre>";
                print_r($user->errors);
                echo "</pre>";
            }
        } else {
            $this->printColorStr("Ошибка! Суперпользователь уже был создан в системе ранее! Невозможно создать 2х суперпользователей.\n", self::COLOR_ERROR);
        }
    }

    /**
     * Создать клики php yii app/create-report-all $amount ($amount - кол-во создаваемых кликов, по умолчанию - 1000)
     * @param int $amount
     */
    public function actionCreateReportAll(int $amount = 1000)
    {

        $users = User::find()->indexBy('id')->all();

        $keys = array_keys($users);

        for ($i = 0; $i < $amount; $i++) {

            $timestamp = mt_rand(time() - 31536000, time());

            $date = date('Y-m-d H:i:s', $timestamp);

            $report = new Report([
                'user_id' => mt_rand(1, count($keys)),
                'date' => $date,
                'day_index' => date('z', $timestamp),
                'week_index' => date('W', $timestamp),
                'month_index' => date('n', $timestamp),
                'year' => date('Y', $timestamp)
            ]);

            $report->save();

        }

    }

    /**
     * Удалить все клики
     */
    public function actionDeleteReport()
    {
        Yii::$app->db->createCommand('TRUNCATE TABLE reports')->execute();
    }

    /**
     * Закрывает все заказы у которых время закрытия <= текущему (раз в 12 часов)
     *
     * php yii app/close-order
     * метод дергается кроном
     */
    public function actionCloseOrder()
    {
        $timeNow = time();

        $orders = Order::find()->where(['published' => 1])->all();

        /**
         * @var Order $order
         */
        foreach ($orders as $order) {

            if ($order->finished_at <= $timeNow) {

                $order->published = 0;
                $order->closing_reason = Order::CLOSING_REASON_TIME;

                $order->save(false);

                $messageForManager = $this->renderPartial('//site/messages/_message-for-customer-close-order', [
                    'order' => $order,
                ]);

                try {
                    Site::sendMessage($messageForManager, $order->email, Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id);
                } catch (Exception $e) {
                }

            }
        }
    }

    /**
     * Рассылает всем заказчикам уведомление, что их заказ закроется через (раз в сутки)
     * Yii::$app->params['dayToSendMailAboutFinishOrder'] дней
     *
     * Дергается кроном. Крон выполняется _1_ (ОДИН) раз в сутки
     *
     * php yii app/send-mail-before-close-order
     *
     */
    public function actionSendMailBeforeCloseOrder()
    {
        $dayToClose = Yii::$app->params['dayToSendMailAboutFinishOrder'];

        $timeNow = time();

        $orders = Order::find()->where(['published' => 1])->all();

        /**
         * @var Order $order
         */
        foreach ($orders as $order) {

            $diff = round(($order->finished_at - $timeNow) / 3600 / 24);

            if ($diff == $dayToClose) {
                $messageForManager = $this->renderPartial('//site/messages/_message-for-customer-before-close-order', [
                    'order' => $order,
                ]);

                try {
                    Site::sendMessage($messageForManager, $order->email, Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id);
                } catch (Exception $e) {
                }
            }

        }
    }

    /**
     * Рассылает письма сметчикам о новых заказах (предполагается цикл ~10 мин + параметр 100)
     *
     * @param int $limitMail
     * @throws \Throwable
     */
    public function actionSendMailToUserAboutNewOrder($limitMail = 100)
    {
        /** @var array $errorSendMailCounter - словили ли Exception при рассылке и на каком именно email'е */
        $errorSendMailCounter = [
            'count' => 0,
            'emails' => []
        ];

        /**
         * @var OrderUser[] $orderHasUser - соттветвтия Заказов Сметитчкам, берем отсортированные по
         */
        $orderHasUser = OrderUser::find()
            ->innerJoin('users', 'users.id = order_user.user_id')
            ->andWhere(['users.status_id' => Yii::$app->params['status']['accepted']])
            ->andWhere(['users.is_visible' => 1])
            ->innerJoin('order', 'order.id = order_user.order_id')
            ->andWhere(['order.published' => 1])
            ->andWhere(['order.checked' => 1])
            ->orderBy(['user_updated' => SORT_DESC])
            ->each();

        $i = 0;
        /** @var OrderUser $OrderForUser */
        foreach ($orderHasUser as $OrderForUser) {

            $user = User::findOne(['id' => $OrderForUser->user_id]);
            $order = Order::findOne(['id' => $OrderForUser->order_id]);;

            if (Yii::$app->params['swichForSendMailOfNewOrder'] == 1) {

                $messageForManager = $this->renderPartial('//site/messages/_message-to-user-about-new-order', [
                    'order' => $order,
                    'user' => $user
                ]);

                try {
                    if (Site::sendMessage($messageForManager, $user->email, Yii::$app->params['messageSubjects']['mailSubjectOrderForCustomer'] . $order->id)) {
                        if (!$OrderForUser->delete()) {
                            echo "error delete!";
                        }
                    };
                } catch (\Exception $e) {
                    $errorSendMailCounter['count'] = $errorSendMailCounter['count']++;
                    $errorSendMailCounter['emails'][] = $user->email;
                }
            }

            $i++;
            if ($i >= $limitMail) break;
        }

    }

    /**
     * Смена пароля администратора
     *
     * @throws \yii\base\Exception
     */
    public function actionChangeAdminPass()
    {

        echo 'Введите новый пароль администратора: ';
        $pass = CliPrompt::hiddenPrompt();

        if (!empty($pass)) {

            echo 'Повторите пароль: ';
            $passCheck = CliPrompt::hiddenPrompt();
            if ($pass === $passCheck) {

                $auth = Auth::find()->where([
                    'login' => 'admin',
                    'is_admin' => 1,
                ])->one();

                $auth->scenario = 'register';

                if (!empty($auth)) {
                    $auth->password = Yii::$app->security->generatePasswordHash($passCheck);

                    if ($auth->save(true, ['password'])) {
                        echo 'Пароль успешно обновлен!' . PHP_EOL;
                    };
                } else {
                    echo 'Ошибка сохранения пароля!' . PHP_EOL;
                }

            } else {
                echo 'Ошибка подтверждения пароля!' . PHP_EOL;
            }
        } else {
            echo 'Ошибка ввода пароля!' . PHP_EOL;
        }
    }

    public function actionCorrectMigrationTable()
    {
        $db2 = new \yii\db\Connection([
            'dsn' => 'mysql:host=dsite;dbname=cda_smetchik_test',
            'username' => 'root',
            'password' => 'Rktc4i!',
        ]);

        $db2->open();

        $db2->createCommand('SET NAMES utf8')->execute();

        $q = $db2->createCommand("SELECT `version` FROM migration WHERE `version` <> 'm000000_000000_base'")->queryAll();

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS `content`")->execute();
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS `content_type`")->execute();

        Yii::$app->db->createCommand("DELETE FROM migration WHERE `version` <> 'm000000_000000_base'")->execute();

        foreach ($q as $item) {
            Yii::$app->db->createCommand(
                "INSERT into migration(version, apply_time) VALUES ('" . $item['version'] . "', " . time() . ")"
            )->execute();
        }

    }

    /**
     * Отправка сообщений пользователям у которых кол-во дней, прошедших с момента последнего обновления, кратное числу, указанному в params.php как remindMessageDivisibleByDays.
     *
     * @return void
     */
    public function actionSendRemindMessageToUsers()
    {
        $users = User::find()->where([
            'is_visible' => 1,
            'status_id' => 2
        ])->all();

        foreach ($users as $user) {

            $daysInactive = round((time() - $user->date_changed) / (60 * 60 * 24));

            if (($daysInactive % Yii::$app->params['remindMessageDivisibleByDays']) == 0) {
                $msg = $this->renderPartial('//site/messages/_message-user-reminder', [
                    'user' => $user,
                    'daysInactive' => $daysInactive
                ]);

                try {
                    Site::sendMessage($msg, $user->email, Yii::$app->params['messageSubjects']['mailUserReminder'] . $user->id);
                } catch (Exception $e) {
                }
            }
        }
    }

    /**
     * Отправка информационных сообщений о новых откликах на заказ (раз в сутки)
     *
     * php yii app/send-email-about-new-feadback-to-customer
     */
    public function actionSendEmailAboutNewFeadbackToCustomer()
    {

        // или только строго под. или всем не заблоченным заказчикама.
        $customers = Customer::find()
            ->where(['status_id' => Customer::STATUS_CONFIRMED['val']])
            ->each();

        $k = 0;
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $k++;
            $ordersCustomerArrForEmail['customer'] = $customer;
            $ordersCustomerArrForEmail['article'] = [];

            // перебираем все заказы заказчика
            $orders = Order::find()
                ->where(['auth_id' =>  $customer->real_id])
                ->andWhere(['published' => 1])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->orderBy(['id' => SORT_DESC])
                ->each();

            if (!empty($orders)) {
                $customerHasNewFeadback = false;

                $i = 0;

                /** @var Order $order */
                foreach ($orders as $order) {

                    $i++;

                    // берем все отклики на заказ начиная с ранних
                    $orderFeadbackUserAll = OrderFeadbackUser::find()
                        ->where(['order_id' => $order->id])
                        ->andWhere(['send' => 0])
                        ->andWhere(['customer_id' => $customer->id])
                        ->orderBy(['created_at' => SORT_ASC])
                        ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                        ->andWhere(['users.status_id' => 2])
                        ->andWhere(['users.is_visible' => 1])
                        ->all();

                    if (!empty($orderFeadbackUserAll)) {
                        $customerHasNewFeadback = true;

                        $orderTitle = "Заказ №" . $order->id . " - " . $order->name;
                        $ordersCustomerArrForEmail['article'][$order->id]['orderTitle'] = $orderTitle;

                        $ordersCustomerArrForEmail['article'][$order->id]['order'] = $order;

                        $users = [];
                        /** @var OrderFeadbackUser $orderFeadbackUser */
                        foreach ($orderFeadbackUserAll as $orderFeadbackUser) {

                            /** @var User $user */
                            $user = User::find()
                                ->where(['id' => $orderFeadbackUser->user_id])
                                ->with('professions')
                                ->with('smetadocs')
                                ->with('normbases')
                                ->one();

                            $users[] = [
                                'user' => $user,
                                'percentUserByOrder' => \app\models\Site::calcPercentUserByOrder($user, $order)
                            ];

                            $orderFeadbackUser->send = 1;

                            if (!$orderFeadbackUser->save()) {
                                print_r($orderFeadbackUser->errors);
                            }
                        }

                        $ordersCustomerArrForEmail['article'][$order->id]['users'] = $users;

                    }
                }

                // нет отзывов - нет и письма
                if ($customerHasNewFeadback) {

                    $messageForCustomer = $this->renderPartial('//customer/messages/_message-to-customer-about-new-feadbacks', [
                        'ordersCustomerArrForEmail' => $ordersCustomerArrForEmail,
                    ]);

                    try {
                        Site::sendMessage($messageForCustomer, $customer->email,
                            Yii::$app->params['messageSubjects']['mailSubjectCustomerNewFeadback']);
                    } catch (Exception $e) {
                    }

                }

                $ordersCustomerArrForEmail = [];
            }
        }
    }

    /**  php yii app/set-all-real-id */
    public function actionSetAllRealId()
    {
        $users = User::find()->where(['real_id' => 0])->all();
        /** @var User $user */
        foreach ($users as $user) {

            /** @var Auth $auth */
            $auth = Auth::find()->where(['user_id' => $user->id])->one();
            $user->real_id = $auth->id;

            $user->save(false);
        }

        $customers = Customer::find()->where(['real_id' => 0])->all();
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            /** @var Auth $auth */
            $auth = Auth::find()->where(['customer_id' => $customer->id])->one();
            $customer->real_id = $auth->id;

            $customer->save(false);
        }
    }

    /**
     * Создание кабинетов для заказчиков из данных заказов - прошлой версии
     *
     * php yii app/create-cabinet
     */
    public function actionCreateCabinet()
    {
        $orders = Order::find()
            //->where(['auth_id' => 0])
            ->with('professionsNorm')
            ->with('professions')
            ->with('smetaDocsNorm')
            ->with('normBasesNorm')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('phones')
            ->all();

        /** @var Order $order */
        foreach ($orders as &$order) {

            $order->auth_id = 0;
            $order->orderSetRelatedOption();
            $order->save(false);
            $order->refresh();

            $customer = Customer::find()->where(['email' => $order->email])->one();

            // если такого нет, то
            if (empty($customer)) {

                $customer = new Customer();

                $customer->scenario = Customer::SCENARIO_REGISTER;
                $customer->name = $order->fio;
                $customer->email = $order->email;

                /** @todo уточнить */
                $customer->created_at = $order->created_at;
                $customer->status_id = Customer::STATUS_REQUIRES_VERIFICATION['val'];

                $passCheck = Site::generatePassword(8);
                $customer->password = $passCheck;
                $customer->rePassword = $passCheck;
                $customer->real_pass = $passCheck;

                $arrPhone = [];
                foreach ($order->phones as $phone) {
                    $arrPhone[] = $phone->number;
                }

                $customer->customerPhones = $arrPhone;

                if (!$customer->save()) {
                    echo "\n";
                    print_r($customer->errors);
                    echo "\n";
                } else {

                    $customer->refresh();
                    $order->auth_id = $customer->real_id;
                    if (!$order->save(false)) {
                        echo "\n";
                        print_r($order->errors);
                        echo "\n";
                    }
                }

            } else {
                // а если есть, то добавим заказ к пользователю
                $order->auth_id = $customer->real_id;

                if ($customer->real_id != null) {

                    if (!$order->save(false)) {
                        echo "\n";
                        print_r($order->errors);
                        echo "\n";
                    }

                } else {
                    echo "Заказчик с real_id == null \n";
                    print_r($customer);
                    echo "\n";
                }
            }
        }
    }


    /**
     * Генерации подходящих заказов для конкретных Сметчиков => для рассылки писем о новых заказах (5 минут)
     *
     * @return bool
     *
     * @author Ilya <ilya.v87v@gmail.com> a.k.a. @decilya
     * @date 19.08.2019
     */
    public function actionSetValueToTblOrderForUsers2()
    {
        /** @var Order $orders - все подтвержденные, проверенные заказы у подтвержденных Заказчиков
         *  по которым не был сформирован пул соответствий для Сметчиков
         */
        $orders = Order::find()
            ->andWhere(['formed' => 0])
            ->andWhere(['published' => 1])
            ->andWhere(['checked' => 1])
            ->innerJoin('auth', 'auth.id = order.auth_id')
            ->innerJoin('customer', 'customer.id = auth.customer_id')
            ->andWhere(['customer.status_id' => Customer::STATUS_CONFIRMED['val']])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('professionsNorm')
            ->with('smetaDocsNorm')
            ->with('normBasesNorm')
            ->all();

        $errorCount = 0;
        /** @var Order $order */
        foreach ($orders as $order) {

            $users = User::find()
                ->where(['status_id' => '2'])
                ->andWhere(['is_visible' => 1])
                ->with('professions')
                ->each();

            /** @var User $user */
            foreach ($users as $user) {

                if (Order::checkStaticOrderProfessionForUser($user->id, $order->id)) {

                    // если пользователь удовлеторяет списку профобл. заказа то запишим его на рассылку писем:
                    // проверив что такой записи у нас еще нет
                    /** @var OrderUser $OrderForUser */
                    $OrderForUser = OrderUser::find()
                        ->where(['order_id' => $this->id])
                        ->andWhere(['user_id' => $user->id])
                        ->one();

                    if (empty($OrderForUser)) {

                        $OrderForUser = new OrderUser();
                        $OrderForUser->order_id = $order->id;
                        $OrderForUser->user_id = $user->id;
                        $OrderForUser->user_updated = $user->date_changed;

                        if (!$OrderForUser->save()) {
                            $errorCount++;
                            echo "\n";
                            print_r($OrderForUser->errors);
                            echo "\n";
                        }

                    }
                }
            }

            $order->formed = 1;
            $order->isNoUpdate = 1;
            $order->save(false);

        }

        if ($errorCount > 0) {
            $this->printColorStr(
                "ВНИМАНИЕ! Во время выполенения произошли ОШИБКИ в кол-ве: " . $errorCount . " шт.!",
                self::COLOR_ERROR
            );

            return false;
        }

        return true;
    }

}
