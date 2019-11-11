<?php

namespace app\models;

use app\controllers\CustomerController;
use app\models\traits\ExtraPhonesTrait;
use tests\codeception\_controllers\Smet4ikControllers;
use Yii;
use yii\db\ActiveRecord;
use app\components\validators\CustomEmailValidator;
use yii\di\Container;
use app\models\Site;
use yii\web\Controller;

//* @property string $link - вырезал из проекта саму суть ссылки для редактирования

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $auth_id
 * @property integer $user_change_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $finished_at
 * @property string $name
 * @property string $fio
 * @property string $phone`
 * @property string $email
 * @property integer $price
 * @property string $text
 *
 * @property Customer $customer
 *
 * @property integer $published
 * @property integer $checked
 * @property integer $closing_reason
 * @property string $closing_reason_text
 *
 * @property array $professions;
 * @property array $smetaDocs;
 * @property array $normBases;
 *
 * @property Profession[] $professionsNorm;
 * @property SmetaDoc[] $smetaDocsNorm;
 * @property NormBase[] $normBasesNorm;
 *
 * @property OrderFeadbackUser[] $userFeadback
 *
 * @property Auth $auth
 *
 * @property int $customerId
 * @property bool $notSaveUpdatedAt
 *
 * @property int $formed;
 */
class Order extends ActiveRecord
{
    use ExtraPhonesTrait;

    const PROPERTY_PROFESSIONS = 'professions';
    const PROPERTY_SMETA_DOCS = 'smetadocs';
    const PROPERTY_NORM_BASES = 'normbases';

    public $professions;
    public $smetaDocs;
    public $normBases;

    public $user_agreement;
    public $byAgreement;

    public $phone;
    public $extraPhones;

    public $dirtyPhoneNumbers = false;

    public $phoneArr;

    public $importantAttributesChanged = false;

    public $isNoUpdate;

    /** помогли сметчики с портала */
    const CLOSING_REASON_OUR = 1;

    /** нашел специалистов в другом месте */
    const CLOSING_REASON_ANOTHER = 2;

    /** иная причина закрытия */
    const CLOSING_REASON_OTHER = 3;

    /** закрыл админ */
    const CLOSING_REASON_ADMIN = 4;

    /** закрыто по истечению времени */
    const CLOSING_REASON_TIME = 5;

    /** закрыто по причине отклонения профиля заказчика */
    const CLOSING_REASON_CLOSED_ORDER = 6;

    public $notSaveUpdatedAt = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'fio', 'email', 'text', 'professions', 'phone'], 'required', 'message' => 'Это обязательное поле'],

            ['price', 'required', 'whenClient' => "function (attribute, value) {
        		return $('#byAgreement').prop('checked') == false;
    		}", 'message' => 'Это обязательное поле'],

            [['auth_id', 'user_change_id', 'created_at', 'updated_at', 'finished_at', 'price', 'published', 'checked', 'closing_reason'], 'integer', 'message' => 'Введите число'],
            [['name', 'fio', 'phone', 'email', /*'link',*/
                'closing_reason_text'], 'string', 'max' => 255],
            [['smetaDocs', 'normBases'], 'each', 'rule' => ['integer']],
            [['text'], 'string', 'max' => 1000, 'tooLong' => 'Максимальная длина заказа 1000 символов'],
            [['email'], 'string', 'max' => 129, 'tooLong' => 'Поле должно содержать не более 129 символов'],
            [['email'], 'trim'],
            ['email', CustomEmailValidator::className(), 'message' => 'Некорректный адрес'],
            [['phone'], 'myValidateForPhoneNumber', 'skipOnEmpty' => false],

            [['phone'], 'match', 'pattern' => '/^79|^\+7\(9/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'match', 'pattern' => '/^((?!_).)*$/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'string', 'min' => 11, 'tooShort' => 'Некорректный номер'],

            [['price'], 'integer', 'max' => 9999999999, 'tooBig' => 'Введите сумму менее 10 миллиардов руб.'],
            [['formed'], 'integer', 'max' => 1],
            ['extraPhones', 'safe'],

            ['user_agreement', 'compare', 'compareValue' => 1, 'message' => 'Необходимо согласиться с правилами сервиса.'],
        ];
    }

    public function myValidateForPhoneNumber($attribute, $params)
    {
        if (strpos($this->phone, '_')) {
            $this->addError($attribute, 'Пожалуйста, введите верный номер мобильного телефона');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_id' => 'User ID',
            'user_change_id' => 'User Change ID',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'finished_at' => 'Дата завершения',
            'name' => 'Название заказа',
            'fio' => 'Ф.И.О.',
            'phone' => 'Телефон',
            'email' => 'Адрес электронной почты',
            'price' => 'Бюджет (руб.)',
            'text' => 'Содержание заказа',
            'published' => 'Опубликовано',
            'checked' => 'Проверено',

            'closing_reason' => 'Closing Reason',
            'closing_reason_text' => 'Closing Reason Text',

            'professions' => 'Профессиональная область',
            'smetaDocs' => 'Сметная документация',
            'normBases' => 'Нормативные базы',
        ];
    }

    public function __construct()
    {
        $this->isNoUpdate = 0;

        parent::__construct();
    }

    /**
     * @inheritdoc
     **
     */
    public function init()
    {
        parent::init();

        /** @var Order $orderTmp */
        $orderTmp = Order::find()
            ->where(['id' => $this->id])
            ->with('professions')
            ->with('smetaDocs')
            ->with('normBases')
            ->with('phones')
            ->all();

        if (!empty($orderTmp)) {

            $this->professions = $orderTmp->getRelatedTitleFrom('professions');
            $this->smetaDocs = $orderTmp->getRelatedTitleFrom('smetaDocs');
            $this->normBases = $orderTmp->getRelatedTitleFrom('normBases');

            $this->orderSetRelatedOption();
        }
    }


    public function getAuth()
    {

        return $this->hasOne(Auth::className(), ['id' => 'auth_id']);
    }

    public function getCustomerId()
    {
        return isset($this->auth->customer_id) ? $this->auth->customer_id : null;
    }


    /**
     * Заказчик
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customerId']);
    }

    /**
     * Relation with Profession Model.
     */
    public function getProfessions()
    {
        // professions_id во множественном числе использовать очень коряво, но дабы не править множество мест пока оставим
        return $this->hasMany(Profession::className(), ['id' => 'professions_id'])->viaTable('order_has_professions', ['order_id' => 'id'])->asArray();
    }

    /**
     * Relation with SmetaDoc Model.
     */
    public function getSmetaDocs()
    {
        return $this->hasMany(SmetaDoc::className(), ['id' => 'smeta_docs_id'])->viaTable('order_has_smeta_docs', ['order_id' => 'id'])->asArray();
    }

    /**
     * Relation with NormBase Model.\
     */
    public function getNormBases()
    {
        return $this->hasMany(NormBase::className(), ['id' => 'normative_bases_id'])->viaTable('order_has_normative_bases', ['order_id' => 'id'])->asArray();
    }

    /**
     * Relation with Profession Model.
     */
    public function getProfessionsNorm()
    {
        // professions_id во множественном числе использовать очень коряво, но дабы не править множество мест пока оставим
        return $this->hasMany(Profession::className(), ['id' => 'professions_id'])->viaTable('order_has_professions', ['order_id' => 'id'])->asArray();
    }

    /**
     * Relation with SmetaDoc Model.
     */
    public function getSmetaDocsNorm()
    {
        return $this->hasMany(SmetaDoc::className(), ['id' => 'smeta_docs_id'])->viaTable('order_has_smeta_docs', ['order_id' => 'id'])->asArray();
    }

    /**
     * Relation with NormBase Model.\
     */
    public function getNormBasesNorm()
    {
        return $this->hasMany(NormBase::className(), ['id' => 'normative_bases_id'])->viaTable('order_has_normative_bases', ['order_id' => 'id'])->asArray();
    }

    public function getUserFeadback()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('order_feadback_user', ['order_id' => 'id'])->asArray();
    }

    public function getNewUserFeadback($id = null, $auth = null)
    {
        if ($id == null) {
            $orderFeadbackUser = OrderFeadbackUser::find()
                ->where(['new' => 1])
                ->andWhere(['order_id' => $this->id])
                ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                ->andWhere(['order.published' => 1])
                ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                ->andWhere(['users.status_id' => 2])
                ->andWhere(['users.is_visible' => 1])
                ->asArray()
                ->all();
        } else {

            switch ($auth) {
                case 'auth':

                    /** @var Auth $auth */
                    $auth = Auth::findOne(['id' => $id]);

                    if ($auth->customer_id != null) {
                        $orderFeadbackUser = OrderFeadbackUser::find()
                            ->where(['new' => 1])
                            ->andWhere(['order_id' => $this->id])
                            ->andWhere(['customer_id' => $id])
                            ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                            ->andWhere(['order.published' => 1])
                            ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                            ->andWhere(['users.status_id' => 2])
                            ->andWhere(['users.is_visible' => 1])
                            ->asArray()
                            ->all();
                    } else {
                        $orderFeadbackUser = OrderFeadbackUser::find()
                            ->where(['new' => 1])
                            ->andWhere(['order_id' => $this->id])
                            ->andWhere(['user_id' => $id])
                            ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                            ->andWhere(['order.published' => 1])
                            ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                            ->andWhere(['users.status_id' => 2])
                            ->andWhere(['users.is_visible' => 1])
                            ->asArray()
                            ->all();
                    }

                    break;

                case 'user':

                    $orderFeadbackUser = OrderFeadbackUser::find()
                        ->where(['new' => 1])
                        ->andWhere(['order_id' => $this->id])
                        ->andWhere(['user_id' => $id])
                        ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                        ->andWhere(['order.published' => 1])
                        ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                        ->andWhere(['users.status_id' => 2])
                        ->andWhere(['users.is_visible' => 1])
                        ->asArray()
                        ->all();

                    break;

                case 'customer':
                    $orderFeadbackUser = OrderFeadbackUser::find()
                        ->where(['new' => 1])
                        ->andWhere(['order_id' => $this->id])
                        ->andWhere(['customer_id' => $id])
                        ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                        ->andWhere(['order.published' => 1])
                        ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                        ->andWhere(['users.status_id' => 2])
                        ->andWhere(['users.is_visible' => 1])
                        ->asArray()
                        ->all();

                    break;

                default:
                    $orderFeadbackUser = OrderFeadbackUser::find()
                        ->where(['new' => 1])
                        ->andWhere(['order_id' => $this->id])
                        ->andWhere(['customer_id' => $id])
                        ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                        ->andWhere(['order.published' => 1])
                        ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                        ->andWhere(['users.status_id' => 2])
                        ->andWhere(['users.is_visible' => 1])
                        ->asArray()
                        ->all();
            }
        }

        return $orderFeadbackUser;
    }

    /**
     * Подходящие пользователи
     **/
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('order_user', ['order_id' => 'id']);
    }

    public function getProfessionsId()
    {
        $tmpArr = [];
        foreach ($this->professions as $profession) {
            $tmpArr[] = $profession['id'];
        }

        return $tmpArr;
    }

    public function getSmetaDocsId()
    {
        $tmpArr = [];
        foreach ($this->smetaDocs as $smetaDoc) {
            $tmpArr[] = $smetaDoc['id'];
        }

        return $tmpArr;
    }

    public function getNormBasesId()
    {
        $tmpArr = [];
        foreach ($this->normBases as $normBases) {
            $tmpArr[] = $normBases['id'];
        }

        return $tmpArr;
    }

    /**
     *
     * Перед сохранением заказа
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if (($this->closing_reason != null) || ($this->closing_reason_text != '')) {

                if ((isset(Yii::$app->user->identity->is_admin)) && ((Yii::$app->user->identity->is_admin))) {
                    // сделать что-то если админ
                } else {

                    if (!$this->notSaveUpdatedAt) {
                        $this->updated_at = time();
                    }

                    if ($this->published == 1) {
                        $this->finished_at = strtotime("+" . Yii::$app->params['dayOfProlongationForOrder'] . " day", time());

                        /**
                         *
                         * При изменении Заказа необходимо менять его статус на «Опубликован (требует проверки)», если были изменены следующие поля:
                         *   - Название заказа,
                         *   - Ф.И.О.,
                         *   - Адрес электронной почты,
                         *   - Телефон,
                         *   - Бюджет (руб.),
                         *   - Содержание заказа.
                         *
                         **/

                        $oldAttributes = $this->getOldAttributes();

                        if (($this->checked == 1) && (!empty($oldAttributes))) {
                            if (($this->name != $oldAttributes['name']) ||
                                ($this->fio != $oldAttributes['fio']) ||
                                ($this->email != $oldAttributes['email']) ||
                                ($this->dirtyPhoneNumbers) ||
                                ($this->price != $oldAttributes['price']) ||
                                ($this->text != $oldAttributes['text'])) {
                                $this->checked = 0;
                                $this->importantAttributesChanged = true;
                            }
                        }

                    }
                }


                if ($this->published === 0) {
                    $this->checked = 1;
                }

            }

            return true;
        }


        return false;
    }

    /**
     * После сохранения
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /// Связи с Profession, SmetaDoc и NormBase:

        /** @todo придумать какой-нибудь единый метод для всех таблиц */

        //если это не закрытие заказа, то

        if ($this->published != 0) {
            if ($this->isNoUpdate == 0) {

                // Если это новая запись
                if ($insert) {

                    // Сохраним профобласти
                    foreach ($this->professions as $profession) {
                        $professionObj = Profession::find()->where(['id' => $profession])->one();
                        $this->link('professions', $professionObj);
                    }

                    // Сохраним cметную документацию
                    if (!empty($this->smetaDocs)) {
                        foreach ($this->smetaDocs as $smetaDoc) {
                            $smetaDocObj = SmetaDoc::find()->where(['id' => $smetaDoc])->one();
                            $this->link('smetaDocs', $smetaDocObj);
                        }
                    }

                    // Сохраним нормативные базы
                    if (!empty($this->normBases)) {
                        foreach ($this->normBases as $normBase) {
                            $normBaseObj = NormBase::find()->where(['id' => $normBase])->one();
                            $this->link('normBases', $normBaseObj);
                        }
                    }

                    // $this->setValueToTblOrderUsers();

                    // Yii::$app->session->setFlash('success', 'Заказ успешно добавлен в базу.');

                } else {
                    // иначе - это обновление

                    if (!empty($this->professions) && count($this->professions) > 0) {
                        // Профобласти. Удалим все что было в нашей таблице Многие-ко-многим про этот заказ
                        $professionsOldObj = OrderHasProfessions::find()->where(['order_id' => $this->id])->all();
                        foreach ($professionsOldObj as $oldProfession) {
                            $oldProfession->delete();
                        }

                        // ...и запишем заново
                        foreach ($this->professions as $profession) {
                            $professionObj = Profession::find()->where(['id' => $profession])->one();
                            $this->link('professions', $professionObj);
                        }
                    }


                    if (!empty($this->smetaDocs) && count($this->smetaDocs) > 0) {
                        // Cметная документация. Удалим все что было в нашей таблице Многие-ко-многим про этот заказ
                        $smetaDocsOldObj = OrderHasSmetaDocs::find()->where(['order_id' => $this->id])->all();
                        if (!empty($smetaDocsOldObj)) {
                            foreach ($smetaDocsOldObj as $oldSmetaDoc) {
                                $oldSmetaDoc->delete();
                            }
                        }

                        // ...и запишем заново
                        if (!empty($this->smetaDocs)) {
                            foreach ($this->smetaDocs as $smetaDoc) {
                                $smetaDocObj = SmetaDoc::find()->where(['id' => $smetaDoc])->one();
                                $this->link('smetaDocs', $smetaDocObj);
                            }
                        }
                    }


                    if (!empty($this->normBases) && count($this->normBases) > 0) {
                        // Нормативные базы. Удалим все что было в нашей таблице Многие-ко-многим про этот заказ
                        $normBasesOldObj = OrderHasNormativeBases::find()->where(['order_id' => $this->id])->all();
                        if (!empty($normBasesOldObj)) {
                            foreach ($normBasesOldObj as $oldNormBase) {
                                $oldNormBase->delete();
                            }
                        }

                        // ...и запишем заново
                        if (!empty($this->normBases)) {
                            foreach ($this->normBases as $normBase) {
                                $normBaseObj = NormBase::find()->where(['id' => $normBase])->one();
                                $this->link('normBases', $normBaseObj);
                            }
                        }
                    }


                    if (!Yii::$app instanceof Yii\console\Application) {
                        Yii::$app->session->setFlash('success', 'Заказ №' . $this->id . ' успешно обновлён!');
                    }
                }

                /// Рассчет связей заказа с пользователем:

                // тут дергается метод, например
                //$this->setValueToTblOrderUsers();
            }
        }
    }

    /**
     * Перед удалением
     *
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        // удалим все данные в связанных таблицах
        $professions = OrderHasProfessions::find()->where(['order_id' => $this->id])->all();
        foreach ($professions as $profession) {
            $profession->delete();
        }

        $smetaDocs = OrderHassmetaDocs::find()->where(['order_id' => $this->id])->all();
        foreach ($smetaDocs as $smetaDoc) {
            $smetaDoc->delete();
        }

        $normativeBases = OrderHasNormativeBases::find()->where(['order_id' => $this->id])->all();
        foreach ($normativeBases as $normativeBase) {
            $normativeBase->delete();
        }

        return true;
    }

    /**
     * Метод заполняет переменные  класса $professions, $smetaDocs и $normBases массивом значений справочников (счетчик => id)
     * (необходимо для вывода во вьюхе в select2)
     *
     * @return bool
     */
    public function orderSetRelatedOption()
    {
        $relatedRecords = $this->getRelatedRecords();

        foreach ($relatedRecords as $key => $related) {
            $resultArr = [];

            $i = 0;
            foreach ($related as $item) {
                $resultArr[] = $item['id'];
                $i++;
            }

            if ($key == 'professions') {
                $this->professions = $resultArr;
            } elseif ($key == 'smetaDocs') {
                $this->smetaDocs = $resultArr;
            } elseif ($key == 'normBases') {
                $this->normBases = $resultArr;
            }

        }

        return true;
    }

    /**
     * Метод возваращает массив заполенный заголовками значений из связанных с Заказом таблиц.
     *
     * @param $myKey
     * @return array
     */
    public function getRelatedTitleFrom($myKey)
    {
        $relatedRecords = $this->getRelatedRecords();

        foreach ($relatedRecords as $key => $related) {
            $resultArr = [];

            $i = 0;
            foreach ($related as $item) {
                $resultArr[$i]['id'] = $item['id'];
                $resultArr[$i]['title'] = $item['title'];
                $i++;
            }

            if ($key == $myKey) {
                return $resultArr;
            }
        }

        return null;
    }


    public function setValueToTblOrderUsers()
    {
        /** @var User $user */
        foreach ($users = User::find()->where(['status_id' => '2'])
            ->andWhere(['is_visible' => 1])
            ->with('professions')->each() as $user) {

            if ($this->checkOrderProfessionForUser($user->id)) {

                // если пользователь удовлеторяет списку профобл. заказа то запишим его на рассылку писем:

                // проверив что такой записи у нас еще нет
                $orderUser = OrderUser::find()->where(['order_id' => $this->id])->andWhere(['user_id' => $user->id])->one();

                if (empty($orderUser)) {
                    $this->link('users', $user);
                }
            }
        }

        return false;
    }


    /**
     * Метод проверяет входит ли какой-то контретный пользователь в список профоблостей заказа
     *
     * @param integer $userId
     * @return bool
     */
    public function checkOrderProfessionForUser(int $userId): bool
    {
        $orderProfessions = $this->professions;

        $user = User::find()->with('professions')->where(['id' => $userId])->one();
        $userProfessions = $user->getRelatedRecords();

        if (isset($userProfessions['professions'])) {
            foreach ($userProfessions['professions'] as $userProfession) {
                foreach ($orderProfessions as $orderProfession) {

                    if ($userProfession->id == $orderProfession) {
                        return true;
                        break; // лол
                    }
                }
            }
        }


        return false;
    }

    public static function checkStaticOrderProfessionForUser(int $userId, int $orderId): bool
    {
        /** @var Order $order */
        $order = Order::find()->where(['id' => $orderId])->with('professions')->one();

        /** @var $order ->professions $orderProfessions */
        $orderProfessions = $order->getRelatedRecords('professions');

        $user = User::find()->with('professions')->where(['id' => $userId])->one();
        $userProfessions = $user->getRelatedRecords('professions');

        if (isset($userProfessions['professions'])) {
            foreach ($userProfessions['professions'] as $userProfession) {
                foreach ($orderProfessions as $orderProfession) {
                    if ($userProfession->id == $orderProfession[0]['id']) {

                        return true;
                        break; // лол
                    }
                }
            }
        }

        return false;
    }


    /**
     *Counts and attaches weight and match percent values for every item in result array.
     *
     * @param array $qp Query string parameters.
     * @param array $result Array being affected.
     * @param array $related Array with related tables' data.
     * @return array Returns $result array.
     */
    public static function countWeight($qp, $result, $related)
    {

        $requestWeight = 0;

        $qpForWeight = $qp;
        unset($qpForWeight['city']);
        unset($qpForWeight['sortprice']);
        unset($qpForWeight['showfrom']);

        foreach ($result as $item) {

            $cnt = 0;
            $matches = 0;

            foreach ($qpForWeight as $k => $v) {


                if (!empty($qpForWeight[$k]) && !empty($related[$item['id']][$k])) {

                    $countMatches = Order::countRelatedAttrWeights($item, $k, $qpForWeight, $related);
                    $cnt += $countMatches * Yii::$app->params['sortingWeights'][$k];

                    $matches += $countMatches;

                }
            }

            $result[$item['id']]['weight'] = $cnt;
            $result[$item['id']]['match_percent'] = ($requestWeight > 0 && $matches > 0) ? (int)(100 / ($requestWeight / $matches)) : null;
        }

        return $result;
    }

    /**
     * Counts number of matches of current affected attribute for single item in result array.
     *
     * @param array $item Single item from
     * @param string $attrName Attribute to affect.
     * @param array $qp Query string parameters.
     * @param array $related Array with related tables' data.
     * @return int
     */
    public static function countRelatedAttrWeights($item, $attrName, $qp, $related)
    {
        $ids = [];

        foreach ($related[$item['id']][$attrName] as $option) {

            if (in_array($option, $qp[$attrName])) {
                $ids[] = $option;
            }
        }

        return count($ids);
    }


    /**
     * Prints extra phone numbers as title for bootstrap tooltip.
     * @return string
     */
    public function createAdditionalNumbersSpan()
    {
        $extraPhonesCount = $this->extraPhones !== null ? count($this->extraPhones) : 0;

        if ($extraPhonesCount > 0) {
            $numArr = [];
            $numArr[] = $this->phone;
            foreach ($this->extraPhones as $number) {
                $numArr[] = $number;
            }
            $numStr = "<span style='color: red' data-toggle=\"tooltip\" title=\"" . implode('<br>', $numArr) . "\"> (+" . $extraPhonesCount . ")</span>";
        } else {
            $numStr = '';
        }

        return $numStr;
    }

    public function getPhones()
    {
        return $this->hasMany(Phone::className(), ['id' => 'phone_id'])
            ->viaTable('order_phones', ['order_id' => 'id'])
            ->innerJoin('order_phones', 'phones.id = order_phones.phone_id')
            ->orderBy(['order_phones.index' => SORT_ASC]);
    }

    /**
     * Устанавливает телефоны для вывода в форму вьюхи
     *
     * @param CustomerPhone[] $phones
     */
    public function setPhonesByCustomer($phones)
    {
        foreach ($phones as $key => $phone) {
            if ($key == 0) {
                $this->phone = $phone->phone;
            } else {
                $this->extraPhones[($key + 1)] = $phone->phone;
            }
        }
    }

    /**
     *
     * Был ли заказ
     *
     * @return bool|null
     */
    public function thisUserIsResponded()
    {
        if (Yii::$app->user->isGuest) return null;

        return (empty(OrderFeadbackUser::find()
            ->where(['order_id' => $this->id])
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->one())) ? true : false;
    }

    /**
     * Присутсвует ли скилл сметчика в заказе
     *
     * @param string $property
     * @param string $propertyName
     *
     * @return bool
     */
    public function userPropertyInOrder($property, $propertyName)
    {
        switch ($propertyName) {
            case self::PROPERTY_PROFESSIONS:

                foreach ($this->professionsNorm as $professionNorm) {
                    if ($professionNorm['title'] == $property) return true;
                }

                break;

            case self::PROPERTY_SMETA_DOCS:

                foreach ($this->smetaDocsNorm as $smetaDocNorm) {
                    if ($smetaDocNorm['title'] == $property) return true;
                }

                break;

            case self::PROPERTY_NORM_BASES:

                foreach ($this->normBasesNorm as $normBaseNorm) {
                    if ($normBaseNorm['title'] == $property) return true;
                }

                break;

            default:
                false;
        }

        return false;
    }

    public function getClosingReasonTextByReasonId()
    {
        if ($this->closing_reason === null) return false;

        if ($this->closing_reason === Order::CLOSING_REASON_OUR) {
            return "Мне помогли сметчики с портала";
        } elseif ($this->closing_reason === Order::CLOSING_REASON_ANOTHER) {
            return "Нашел специалистов в другом месте";
        } elseif ($this->closing_reason === Order::CLOSING_REASON_OTHER) {
            return "Другая причина";
        } elseif ($this->closing_reason === Order::CLOSING_REASON_TIME) {
            return "Истёк срок публикации";
        }

        return "";
    }

    public function getShutterOrderIsCustomerOrUserOrManager()
    {
        if (($this->user_change_id === null) || ($this->closing_reason == null)) return false;

        $userType = Auth::getUserTypeById($this->user_change_id);

        if ($userType) {

            switch ($userType) {

                case Auth::TYPE_USER:
                    return 'user';
                    break;
                case Auth::TYPE_CUSTOMER:
                    return 'customer';
                    break;
                case Auth::TYPE_ADMIN:
                    return 'admin';
                    break;
                default:
                    return false;
            }

        } else {
            return false;
        }
    }


}