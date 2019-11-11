<?php

namespace app\models;


use MongoDB\BSON\Timestamp;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use app\models\User;

class Site extends Model
{
    const MONTHS = [1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    /**
     * Fetches all static content from Db.
     * @return array
     */
    public static function prepareStaticDBsContent()
    {

        return [
            'cities' => City::find()->asArray()->indexBy('id')->orderBy('name')->all(),
            'professions' => Profession::find()->asArray()->indexBy('id')->orderBy('title')->all(),
            'smetaDocs' => SmetaDoc::find()->asArray()->indexBy('id')->orderBy('title')->all(),
            'normBases' => NormBase::find()->asArray()->indexBy('id')->orderBy('title')->all(),
        ];
    }

    /**
     * Checks Yii::$app->params['messages'] array, which contents are taken from config/params.php, for messages' names and status, then searches for first in session variables.
     * Normally, there can be only one active flash message at time.
     * @return array
     */
    public static function checkFlashMessages()
    {

        $arr = [];

        foreach (Yii::$app->params['messages'] as $k => $v) {

            if (Yii::$app->session->hasFlash($k)) {
                $arr['key'] = $k;
                $arr['body'] = Yii::$app->session->getFlash($k);
                $arr['status'] = Yii::$app->params['messages'][$k]['status'];

                break;
            }

        }

        return $arr;
    }

    /**
     * Parses given query string and returns array with variables.
     * @param string $str query string
     * @return array
     */
    public static function parseQueryString($str)
    {

        if (substr($str, 0, 1) === '?') $str = substr($str, 1);

        $tmp = explode('&', $str);
        $arr = [];
        foreach ($tmp as $item) {

            $tmpArr = explode('=', $item);

            $arr[$tmpArr[0]] = $tmpArr[1];

        }

        return $arr;
    }

    /**
     * Searches each given string in given array and explode it, if exists.
     * @param array $items
     * @param array $arr
     * @return array
     */
    public static function massExplode($items, $arr)
    {

        foreach ($items as $k => $v) {
            if (isset($arr[$v])) {
                $arr[$v] = explode(',', $arr[$v]);
            }
        }

        return $arr;
    }

    /**
     * Searches each given string in given array and implode it, if exists.
     * @param array $items
     * @param array $arr
     * @return array
     */
    public static function massImplode($items, $arr)
    {

        foreach ($items as $k => $v) {

            if (isset($arr[$v])) {
                $arr[$v] = implode(',', $arr[$v]);
            }
        }

        return $arr;
    }

    /**
     * Shortened version of yii2 VarDumper::dump() method with ability to die after output.
     * @param mixed $var
     * @param boolean $die whether die after output or not.
     * @return void
     */
    public static function VD($var, $die = true)
    {
        VarDumper::dump($var, 10, 1);
        if ($die) die();
    }

    /**
     * Method to send email.
     *
     * @param $message
     * @param $email
     * @param $subj
     * @return bool
     */
    public static function sendMessage($message, $email, $subj)
    {
        $email = (!empty(Yii::$app->params['testMail'])) ? Yii::$app->params['testMail'] : $email;
        $email = idn_to_ascii($email, 0, INTL_IDNA_VARIANT_UTS46);
        $headers = "Content-type: text/html; charset=utf-8" . "\r\n";
        $mailFrom = Yii::$app->params['mailFrom'];
        $headers .= "From:  VALINTA.RU <" . $mailFrom . ">\r\n";

        try {
            Yii::$app->mailer->compose('layouts/html', ['content' => $message])
                ->setFrom($mailFrom)
                ->setTo($email)
                ->setSubject($subj)
                ->setTextBody('Plain text content')
                ->setHtmlBody($message)
                ->send();

            return true;
        } catch (\Swift_SwiftException $exception) {
            echo "Swift_SwiftException exception\n";

            return false;

            //return 'Can sent mail due to the following exception'.print_r($exception);
        }
    }

    /**
     * Clear session vars while on authorized area of our app.
     * @return void
     */
    public static function clearFilterSessionVars()
    {

        if (Yii::$app->session->has('keys')) Yii::$app->session->remove('keys');
        if (Yii::$app->session->has('related')) Yii::$app->session->remove('related');
        if (Yii::$app->session->has('staticDBsContent')) Yii::$app->session->remove('staticDBsContent');
        if (Yii::$app->session->has('matchesPercentArr')) Yii::$app->session->remove('matchesPercentArr');
    }

    /**
     * Renders header links.
     *
     * @param string $cell
     * @return string
     */
    public static function renderHeaderLinks($cell)
    {

        $action = Yii::$app->controller->action->id;

        $links = '';

        switch ($cell) {
            case 1:
                if ($action !== 'about' && $action !== 'agreement' && $action !== 'request-agreement') {
                    $links = "<a class='b-homeLink' href='/'>НА ГЛАВНУЮ</a>
                    <div class='hotline'>
                       <span class='hotline-phrase' style='color:#000;'>&laquo;ГОРЯЧАЯ&raquo; ЛИНИЯ</span>
                        <a href='viber://pa?chatURI=valinta' class='hotline-viber' title='viber'></a>
                        <a title=\"Telegram\" href=\"tg://resolve?domain=valinta_bot\" class='hotline-telegram' ></a>
                    </div>";
                } elseif ($action === 'about') {
                    $links = "<a class='registerLinkForm' href='/request'>Сметная программа бесплатно!</a>";
                }
                break;

            case 2:
                if ($action !== 'agreement' && $action !== 'request-agreement' && $action !== 'request' && $action !== 'about') {
                    $links = "<a class='registerLinkForm' href='/request'>Сметная программа бесплатно!</a>";
                } elseif (Yii::$app->user->isGuest && $action === 'request') {
                    $links = '<a class="registerLink" href="/register">Регистрация в базе</a>';
                } elseif ((!Yii::$app->user->isGuest && $action === 'request') || ($action === 'about' && (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user))) {
                    $links = '<a class="mainLink" href="/">Поиск сметчиков</a>';
                }
                break;

            case 3:
                if (Yii::$app->user->isGuest && ($action === 'index' || $action === 'about' || $action === 'content')) {
                    $links = '<a class="registerLink" href="/register">Регистрация в базе</a>';
                } elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user && ($action === 'index' || $action === 'request' || $action === 'about' || $action === 'content')) {
                    $links = '<a class="cabinetLink" href="/user/update/' . Yii::$app->user->identity->user_id . '">Личный кабинет</a>';
                } elseif ((Yii::$app->user->isGuest && ($action === 'register' || $action === 'request')) || ($action === 'update' && (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user))) {
                    $links = '<a class="mainLink" href="/">Поиск сметчиков</a>';
                }
                break;
            case 4:
                if (Yii::$app->user->isGuest && ($action !== 'request-agreement' || $action !== 'agreement' || $action !== 'login')) {
                    $links = '<a class="loginLink" href="/login"><i class="b-top-nav__ico-castle"></i>личный кабинет</a>';
                } elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_user) {
                    if ($action === 'index') {
                        $links = '<a class="logoutLink" href="/site/logout"><i class="b-top-nav__ico-castle"></i>личный кабинет</a>';
                    } elseif ($action !== 'agreement' && $action !== 'request-agreement') {
                        $links = '<a class="logoutLink" href="/site/logout">Выйти</a>';
                    }
                }
                break;
        }

        return $links;
    }

    /**
     * Adds data-text attribute which equals it's text to select2 option list.
     *
     * @param $array
     * @return array
     */
    public static function addDataTextAttributeToSelect2Options($array)
    {

        $result = [];

        foreach ($array as $item) {

            $result[$item['id']] = [
                'data-text' => $item['title']
            ];

        }

        return $result;
    }

    /**
     * Checks if user has IE < 9.
     *
     * @return bool
     */
    public static function checkUserAgent()
    {
        return (bool)preg_match('/(MSIE [1-8]{1}\.)/', Yii::$app->request->userAgent);
    }

    /**
     * Returns russian month names
     * @param string $monthIndex
     * @return string
     */
    public static function russianMonthNames($monthIndex)
    {
        $name = '';
        switch ($monthIndex) {
            case 1:
                $name = 'января';
                break;
            case 2:
                $name = 'февраля';
                break;
            case 3:
                $name = 'марта';
                break;
            case 4:
                $name = 'апреля';
                break;
            case 5:
                $name = 'мая';
                break;
            case 6:
                $name = 'июня';
                break;
            case 7:
                $name = 'июля';
                break;
            case 8:
                $name = 'августа';
                break;
            case 9:
                $name = 'сентября';
                break;
            case 10:
                $name = 'октября';
                break;
            case 11:
                $name = 'ноября';
                break;
            case 12:
                $name = 'декабря';
                break;
            default:
                break;
        }

        return $name;
    }

    /**
     *
     * @param string $unescapedStr
     * @return string
     */
    public static function escapeStringForDateFunc($unescapedStr)
    {
        $arr = str_split($unescapedStr);
        $escapedStr = '';
        foreach ($arr as $char) {
            $escapedStr .= "\\" . $char;
        }
        return $escapedStr;
    }

    public static function removeUnnecessaryGetParameters(&$arr)
    {
        foreach ($arr as $k => $v) if (!in_array($k, ['professions', 'normbases', 'smetadocs', 'city', 'sortFilter', 'sortDirection', 'showfrom'])) unset($arr[$k]);
    }

    /**
     * @param int $time
     * @return false|string
     */
    public static function getNormalRussianDateByTimeStamp($time)
    {
        return date(
            'd ' .
            Site::escapeStringForDateFunc(Site::russianMonthNames(date('n', $time))) .
            ' Y \г\.', $time
        );
    }

    /**
     * Расчет соответствия сметчика заказу в %
     *
     * @param User $user
     * @param Order $order
     * @return int
     *
     */
    public static function calcPercentUserByOrder(User $user, Order $order)
    {
        /** @var User $userRealRelated */
        $userRealRelated = User::find()
            ->where(['id' => $user->id])
            ->with('professions')
            ->with('smetadocs')
            ->with('normbases')
            ->one();

        /** @var Order $orderRealRelated */
        $orderRealRelated = Order::find()
            ->where(['id' => $order->id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->one();

        $orderArrItems = [];

        foreach ($orderRealRelated->getRelatedRecords() as $arr) {
            foreach ($arr as $ar) {
                if (isset($ar['title'])) {
                    $orderArrItems[] = $ar['title'];
                }
            }
        }

        $userArrItems = [];
        foreach ($userRealRelated->getRelatedRecords() as $arr) {
            foreach ($arr as $ar) {
                if (isset($ar['title'])) {
                    $userArrItems[] = $ar['title'];
                }
            }
        }

        $diffCount = [];

        foreach ($userArrItems as $userItem) {

            if (in_array($userItem, $orderArrItems)) {
                $diffCount[] = $userItem;
            }

        }

        if (empty($orderArrItems)) return 0;

        return round(100 * (1 - (count($orderArrItems) - count($diffCount)) / count($orderArrItems)));
    }

    public static function canUserGetContactThisOrder(int $orderId, int $userId, Auth $auth = null): bool
    {
        if ($auth === null) {
            $auth = isset(Yii::$app->user->identity->id) ? Auth::findOne(['id' => Yii::$app->user->identity->id]) : null;
        }

        return ($auth) ? (($auth->user_id) ? ((empty(OrderFeadbackUser::find()
            ->where(['order_id' => $orderId])
            ->andWhere(['user_id' => $userId])
            ->innerJoin('users', 'order_feadback_user.user_id = users.id')
            ->andWhere(['users.status_id' => 2])
            ->andWhere(['users.is_visible' => 1])
            ->one())
        ) ? false : true) : null) : null;
    }

    /**
     *  Метод генерирует пароль, содержащий хотя бы одну заглавную букву и одну цифру.
     *
     * @param int $length
     * @return string
     * @throws \yii\base\Exception
     */
    public static function generatePassword($length = 6)
    {
        // Бесконеный цикл пока не сгенерится строка содержащая хотя бы одну заглавную букву и хотя бы одну цифру
        while (true) {
            $tmpPassword = Yii::$app->security->generateRandomString($length);
            if (strlen(preg_replace('![^A-Z]+!', '', $tmpPassword)) > 0) {
                preg_match("/[\d]+/", $tmpPassword, $match);
                if (isset($match[0])) {
                    if ($match[0] > 0) {
                        return $tmpPassword;
                    }
                }
            }
        }
    }


}