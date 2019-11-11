<?php

namespace app\models;

use app\components\validators\CustomEmailValidator;
use app\models\traits\ExtraPhonesTrait;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property string $phone
 * @property string $experience
 * @property int $has_city
 * @property int $city_id
 * @property int $has_price
 * @property int $price
 * @property string $ipap_attestat_id
 * @property int $status_id
 * @property int $is_visible
 * @property string $reject_msg
 * @property int $date_changed
 * @property int $last_change_by_user
 * @property int $real_id
 *
 * @property Profession[] $professionsNorm
 * @property SmetaDoc[] $smetadocs
 * @property NormBase[] $normbases
 *
 * @property int|null $realID
 * @property City $city
 */
class User extends ActiveRecord
{
    use ExtraPhonesTrait;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    const USER_PHONE_NUMBERS_LIMIT = 3;

    public $professions;
    public $smetaDocs;
    public $normBases;

    public $weight;

    public $password;
    public $password_repeat;

    public $status_msg;

    public $toBeAdded;

    public $user_agreement;

    public $phone;
    public $extraPhones;

    public $dirtyPhoneNumbers = false;

    public $phoneArr;

    public $importantAttributesChanged = false;

    protected $realID; // № профиля Сметчика

    public $email;

    public function scenarios()
    {
        return [
            self::SCENARIO_REGISTER => ['fio', 'email', 'phone', 'extraPhones', 'phoneArr', 'professions', 'password',
                'password_repeat', 'smetaDocs', 'normBases', 'experience', 'reject_msg', 'status_id', 'is_visible',
                'date_changed', 'last_change_by_user', 'has_price', 'city_id', 'ipap_attestat_id', 'price', 'user_agreement', 'real_id'],

            self::SCENARIO_UPDATE => ['fio', 'email', 'phone', 'extraPhones', 'phoneArr', 'professions', 'smetaDocs',
                'normBases', 'experience', 'reject_msg', 'status_id', 'is_visible', 'date_changed', 'last_change_by_user',
                'has_price', 'city_id', 'ipap_attestat_id', 'price', 'real_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'phone', 'professions', 'email'], 'required', 'message' => 'Это обязательное поле'],
            [['password', 'password_repeat', 'user_agreement'], 'required', 'message' => 'Это обязательное поле', 'on' => self::SCENARIO_REGISTER],

            ['password', 'match', 'pattern' => '/[A-ZА-Я]+/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву', 'on' => self::SCENARIO_REGISTER],
            ['password', 'match', 'pattern' => '/\d+/', 'message' => 'Пароль должен содержать хотя бы одну цифру', 'on' => self::SCENARIO_REGISTER],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен быть не менее 6 символов', 'max' => 255, 'tooLong' => 'Пароль должен быть не более 255 символов', 'on' => self::SCENARIO_REGISTER],

            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'message' => 'Повторите пароль для подтверждения', 'on' => self::SCENARIO_REGISTER],

            ['user_agreement', 'compare', 'compareValue' => 1, 'message' => 'Необходимо согласиться с правилами сервиса.', 'on' => self::SCENARIO_REGISTER],

            [['smetaDocs', 'normBases'], 'each', 'rule' => ['integer']],
            [['experience', 'reject_msg'], 'string'],
            [['status_id', 'is_visible', 'date_changed', 'last_change_by_user', 'has_price', 'city_id'], 'integer'],
            [['fio'], 'string', 'max' => 100, 'tooLong' => 'Поле должно содержать не более 100 символов'],
            [['email'], 'string', 'max' => 129, 'tooLong' => 'Поле должно содержать не более 129 символов'],

            ['phone', 'match', 'pattern' => '/^79|^\+7\(9/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            ['phone', 'string', 'min' => 11, 'tooShort' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'match', 'pattern' => '/^((?!_).)*$/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],

            ['ipap_attestat_id', 'string', 'max' => 15, 'tooLong' => 'Поле должно содержать не более 15 символов'],

            ['ipap_attestat_id', 'trim'],
            ['ipap_attestat_id', 'match', 'pattern' => '/^\d+$/', 'skipOnEmpty' => true, 'message' => 'Введите корректный номер аттестата ИПАП'],

            ['price', 'integer', 'message' => 'Пожалуйста, введите стоимость в цифрах без пробелов (от 0 до 9999999)', 'min' => 0, 'tooSmall' => 'Введите число от 0 до 9999999', 'max' => 9999999, 'tooBig' => 'Введите число от 0 до 9999999'],

            [['email', 'ipap_attestat_id'], 'trim'],
            ['email', CustomEmailValidator::className(), 'message' => 'Некорректный адрес', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],

            ['ipap_attestat_id', 'unique', 'message' => 'Такой аттестат уже зарегистрирован'],

            ['email', 'validateRealUniqueEmail', 'on' => self::SCENARIO_REGISTER],
            ['email', 'validateRealUniqueEmailUpdate', 'on' => self::SCENARIO_UPDATE],

        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->id != null) {
            /** @var Auth $auth */
            $auth = Auth::findOne(['user_id' => $this->id]);

            if (isset($auth->login)) {
                $this->email = $auth->login;
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'Ф.И.О.',
            'email' => 'Адрес электронной почты',
            'phone' => 'Мобильный телефон',
            'experience' => 'Образование и опыт работы',
            'city_id' => 'Город',
            'price' => 'Стоимость работ (руб.)',
            'ipap_attestat_id' => 'Номер профессионального аттестата ИПАП (если есть)',
            'status_id' => 'Status ID',
            'is_visible' => 'Is Visible',
            'reject_msg' => 'Причина отклонения анкеты',
            'date_changed' => 'Date Changed',
            'last_change_by_user' => 'Last Change By Smetchik',
            'professions' => 'Профессиональная область',
            'smetaDocs' => 'Сметная документация',
            'normBases' => 'Нормативные базы',
            'password_repeat' => 'Подтверждение пароля',
            'password' => 'Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)',
            'user_agreement' => ''
        ];
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->refresh();

        /** @var Auth $auth */
        if (empty($auth = Auth::find()->where(['user_id' => $this->id])->one())) {

            $auth = new Auth(['scenario' => self::SCENARIO_REGISTER]);
            $auth->login = $this->email;
            $auth->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $auth->user_id = $this->id;
            $auth->customer_id = null;

            if ($auth->save(false)) {

                /** @var User $user */
                $user = User::find()->where(['id' => $this->id])->one();
                $user->real_id = $auth->id;

                if (!$user->update(false)) {
                    Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить значение realID');
                    //$this->delete();
                    return false;
                }

            } else {
                $this->delete();
                Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');
                return false;
            }
        } else {
            $auth->login = $this->email;
            $auth->save(false);
        }

        return true;
    }



//    /**
//     * @param bool $insert
//     * @return bool
//     * @throws \Throwable
//     * @throws \yii\base\Exception
//     * @throws \yii\db\StaleObjectException
//     */
//    public function beforeSave($insert)
//    {
//
//
//        /**
//         *
//         *
//         *
//         */
//
//        /** @var Auth $auth */
//        if (($this->id === null) || (empty($auth = Auth::find()->where(['user_id' => $this->id])->one()))) {
//
//            $auth = new Auth(['scenario' => self::SCENARIO_REGISTER]);
//            $auth->login = $this->email;
//            $auth->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
//            $auth->user_id = $this->id;
//            $auth->customer_id = null;
//            $auth->scenario = Auth::SCENARIO_REGISTER;
//
//            if (!$auth->save()) {
//                $this->delete();
//                Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');
//                return false;
//            }
//        } else {
//
//            $auth->login = $this->email;
//            $auth->scenario = Auth::SCENARIO_REGISTER;
//
//            if (!$auth->save()) {
//                $this->delete();
//                Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');
//                return false;
//            }
//
//
//        }
//
//        return parent::beforeSave($insert);
//    }


    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmail($attribute, $params)
    {
        if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
            $this->addError($attribute, $this->email . " E-mail уже зарегистрирован");
        }

        if ($this->email == '') {
            $this->addError($attribute, "Это обязательное поле");
        }

        $patter = "|[а-яё]|is";
        if (preg_match($patter, $this->email)) {
            $this->addError($attribute, "Кириллические символы недопустимы в email");
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmailUpdate($attribute, $params)
    {
        $auth = Auth::find()->where(['customer_id' => $this->id])->one();

        if (!empty($auth)) {
            if ($auth->login != $this->email) {
                if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
                    $this->addError($attribute, "Такой email уже зарегистирован в системе");
                }
            }
        }

        if ($this->email == '') {
            $this->addError($attribute, "Это обязательное поле");
        }

        $patter = "|[а-яё]|is";
        if (preg_match($patter, $this->email)) {
            $this->addError($attribute, "Кириллические символы недопустимы в email");
        }
    }

    /**
     *Fills in corresponding junction tables from given array.
     *
     * @param array $arr
     * Array properties: [
     *        'modelName' - name of corresponding table class
     *        'modelProp' - name of class property
     *        'tableCol' - name of corresponding table column
     * ]
     * @return void
     */
    public function fillJunctionTables($arr)
    {

        foreach ($arr as $table) {
            $modelNameFull = 'app\\models\\' . $table['modelName'];

            $toBeAdded = (!empty($table['toBeAdded'])) ? $table['toBeAdded'] : $this->{$table['modelProp']};

            if (!empty($toBeAdded) && is_array($toBeAdded)) {
                foreach ($toBeAdded as $item) {
                    $jt = new $modelNameFull;
                    $jt->user_id = $this->id;
                    $jt->{$table['tableCol']} = (int)$item;

                    $jt->save();
                }
            }
        }

    }

    /**
     * Compares old and new model attributes and rearrange corresponding database.
     *
     * @param array $arr
     * Array modelProperties: [
     *        'modelName' - name of corresponding table class
     *        'modelProp' - name of class property
     *        'tableCol' - name of corresponding table column
     * ]
     * @return void
     */
    public function compareJunctionTableEntries($arr)
    {

        foreach ($arr as $tabItem) {

            $delArr = [];

            $modelNameFull = 'app\\models\\' . $tabItem['modelName'];

            if (!empty($this->{$tabItem['modelProp']})) {

                $oldVals = $modelNameFull::find()->where(['user_id' => $this->id])->indexBy($tabItem['tableCol'])->asArray()->all();

                foreach ($this->{$tabItem['modelProp']} as $k => $v) {
                    $this->{$tabItem['modelProp']}[$k] = (int)$v;
                    $delArr[] = (int)$v;
                }

                $toBeDeleted = array_diff(array_keys($oldVals), $delArr);

                if (!empty($toBeDeleted)) {
                    $modelNameFull::deleteAll(['and', 'user_id=' . $this->id, ['in', $tabItem['tableCol'], $toBeDeleted]]);
                }

                $toBeAdded = array_diff($this->{$tabItem['modelProp']}, array_keys($oldVals));

                if (!empty($toBeAdded)) {
                    $this->fillJunctionTables([
                        [
                            'modelName' => $tabItem['modelName'],
                            'modelProp' => $tabItem['modelProp'],
                            'tableCol' => $tabItem['tableCol'],
                            'toBeAdded' => $toBeAdded
                        ]
                    ]);
                }

            } else {
                $modelNameFull::deleteAll(['user_id' => $this->id]);
            }
        }
    }

    /**
     * Prepares User model attributes for saving in DB.
     *
     * @param string $action Name of the action in which this method is triggered.
     * @return void
     */
    public function prepareUserAttributes($action = null)
    {

        $this->fio = strip_tags(trim($this->fio));

        $this->price = (!empty($this->price) ? (int)$this->price : null);
        $this->has_price = (!empty($this->price)) ? 1 : 0;

        $this->city_id = (!empty($this->city_id)) ? (int)$this->city_id : null;
        $this->has_city = (!empty($this->city_id)) ? 1 : 0;

        $this->ipap_attestat_id = (!empty($this->ipap_attestat_id)) ? strip_tags(trim($this->ipap_attestat_id)) : '';
        $this->experience = (!empty($this->experience)) ? strip_tags(trim($this->experience)) : '';

        if ($action === 'register') {
            $this->last_change_by_user = 1;
            $this->date_created = $this->date_changed = time();
        } else {
            $this->checkLastChangedTime();
        }
    }

    /**
     *Prepares actual query for execution.
     *
     * @param bool $limit If query has limit.
     * @param int $offset If query has offset.
     * @param mixed $ids Keys for final sorted query.
     * @return \yii\db\ActiveQuery
     */
    public static function constructQuery($limit = false, $offset = 0, $ids = false)
    {
        $query = User::find();
        $query = User::attachConditionsToQuery($query, $ids);

        if ($limit) $query->limit($limit)->offset($offset);

        return $query;
    }

    /**
     * Determines whether query is keys query or final query and attach corresponding conditions to query.
     *
     * @param \yii\db\ActiveQuery $query
     * @param mixed $ids Affected items' ids.
     * @return \yii\db\ActiveQuery
     */
    public static function attachConditionsToQuery($query, $ids)
    {

        if ($ids === false) {
            //Keys query
            if (!Yii::$app->params['testAllUsersMode']) {
                $query->orWhere(['and', 'is_visible=1', 'status_id=' . Yii::$app->params['status']['accepted']]);
            }
        } else {
            //Final query
            $query->andWhere('id IN(' . $ids . ')');
            $query->orderBy((new Expression('FIND_IN_SET(users.id, "' . $ids . '")')));
        }

        return $query;
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
        unset($qpForWeight['sortFilter']);
        unset($qpForWeight['sortDirection']);
        unset($qpForWeight['showfrom']);

        foreach ($qpForWeight as $item) {
            if (!empty(implode(',', $item))) {
                $requestWeight += count($item);
            }
        }

        foreach ($result as $item) {

            $cnt = 0;
            $matches = 0;

            if (!empty($qp['city']) && (bool)$item['has_city'] && ($qp['city'] == $item['city_id'])) {
                $cnt += Yii::$app->params['sortingWeights']['city'];
            }

            foreach ($qpForWeight as $k => $v) {

                if (!empty($qpForWeight[$k]) && !empty($related[$item['id']][$k])) {

                    $countMatches = User::countRelatedAttrWeights($item, $k, $qpForWeight, $related);
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

            if (!in_array($option, $ids)) $ids[] = $option;
        }
        $arr = array_diff($qp[$attrName], $ids);

        return (count($qp[$attrName]) - count($arr));
    }

    /**
     * Determines argument for sql query and prepares data from junction tables for result items on main page.
     *
     * @param mixed $ids Specialize item ids for fetching or null for all items.
     * @return array
     */
    public static function prepareRelatedArrays($ids = null)
    {
        if (!empty($ids) && !Yii::$app->params['testAllUsersMode']) {
            $idsStr = implode(',', $ids);
            $arg = 'IN (' . $idsStr . ')';
        } else {
            $arg = '> 0';
        }

        $related = [];

        $related = User::fetchRelatedTables([
            [
                'tableName' => 'user_has_professions',
                'tableField' => 'profession_id',
                'arrKey' => 'professions'
            ], [
                'tableName' => 'user_has_smeta_docs',
                'tableField' => 'smeta_docs_id',
                'arrKey' => 'smetadocs'
            ], [
                'tableName' => 'user_has_normative_bases',
                'tableField' => 'normative_bases_id',
                'arrKey' => 'normbases'
            ]
        ], $related, $arg);

        if (count($ids) !== count($related)) {
            foreach (array_diff($ids, array_keys($related)) as $item) {
                $related[$item] = null;
            }
        }

        return $related;
    }

    /**
     * Fills $related array with junction table data.
     *
     * @param array $arr
     * Array modelProperties: [
     *        'modelName' - name of corresponding table class
     *        'modelProp' - name of class property
     *        'tableCol' - name of corresponding table column
     * ]
     * @param array $related Array to be filled with related tables data.
     * @param string $arg Sql query where condition.
     * @return array $related
     */
    public static function fetchRelatedTables($arr, $related, $arg)
    {
        foreach ($arr as $table) {
            $sql = 'SELECT user_id, ' . $table['tableField'] . ' FROM ' . $table['tableName'] . ' WHERE user_id ' . $arg;
            $items = User::findBySql($sql)->asArray()->all();

            foreach ($items as $item) {
                $related[$item['user_id']][$table['arrKey']][] = $item[$table['tableField']];
            }
        }

        return $related;
    }

    /**
     * This method fetches data from junction tables and attaches it to model.
     * This is an alternative to ActiveQuery with() method.
     *
     * @param array $arr
     * Array modelProperties: [
     *        'modelName' - name of corresponding table class
     *        'modelProp' - name of class property
     *        'tableCol' - name of corresponding table column
     * ]
     * @return void
     */
    public function fillUserWithJunctionTablesData($arr)
    {
        foreach ($arr as $item) {
            $this->{$item['modelProp']} = User::fetchRelatedTableForUserUpdate($item['modelName'], $item['tableCol'], $this->id);
        }
    }

    /**
     *Fetches single related table and returns all keys.
     *
     * @param string $modelName Name of model.
     * @param string $tableCol Name of table column.
     * @param integer $id Id of the affected model.
     * @return array Array with keys.
     */
    public static function fetchRelatedTableForUserUpdate($modelName, $tableCol, $id)
    {
        $modelNameFull = 'app\\models\\' . $modelName;

        $arr = $modelNameFull::find()->select([$tableCol])->where(['user_id' => $id])->indexBy($tableCol)->asArray()->all();

        return array_keys($arr);
    }

    /**
     * Constructs proper query for admin pages filtering.
     *
     * @param array $qp Received request query string params.
     * @param string $model Model to search in.
     * @return array $arr Array with filter parameters and actual query.
     */
    public static function constructAdminFiltersQuery($qp, $model = 'User')
    {
        $authTbl = false;
        $arr = [
            'filterParams' => []
        ];

        $model = 'app\\models\\' . $model;

        $query = $model::find();
        if (!empty($qp['id'])) {

            if ($model == 'app\models\Request') {
                $query->andFilterWhere(['id' => (int)$qp['id']]);
            } else {
                $query->innerJoin('auth', '`auth`.`user_id` = `users`.`id`');
                $authTbl= true;
                $query->andFilterWhere(['auth.id' => (int)$qp['id']]);
            }

            $arr['filterParams']['id'] = (int)$qp['id'];
        }

        if (!empty($qp['text'])) {

            $text = (!empty($qp['text']) ? strip_tags(trim($qp['text'])) : "");

            if ($model == 'app\models\User') {
                if (!$authTbl) {
                    $query->innerJoin('auth', '`auth`.`user_id` = `users`.`id`');
                }
                $authTbl= true;
                $query->andFilterWhere(['or', ['like', 'fio', $text], ['like', 'auth.login', $text]]);

                $phone = Phone::find()->where([
                    'like',
                    'number',
                    $text
                ])->indexBy('id')->all();

                if (!empty($phone)) {
                    $query->orFilterWhere(['in', 'id', (new Query())->select('user_id')->from('users_phones')->where(['in', 'phone_id', array_keys($phone)])]);
                }
            } else {
                if (!$authTbl) {
                    $query->innerJoin('auth', '`auth`.`user_id` = `users`.`id`');
                }
                $authTbl= true;
                $query->andFilterWhere(['or', ['like', 'fio', $text], ['like', 'auth.login', $text], ['like', 'phone', $text]]);
            }

            $arr['filterParams']['text'] = $text;
        }

        if (!empty($qp['userStatus'])) {
            $query->andFilterWhere(['status_id' => (int)$qp['userStatus']]);
            $arr['filterParams']['userStatus'] = (int)$qp['userStatus'];
        }

        if (!empty($qp['status'])) {
            $query->andFilterWhere(['status_value' => (int)$qp['status']]);
            if ($model == 'app\models\Request') $arr['filterParams']['status'] = (!empty($qp['status'])) ? (int)$qp['status'] : "";
        }

        if (!empty($qp['page'])) $arr['filterParams']['page'] = $qp['page'];

        $arr['query'] = $query;

        return $arr;
    }

    /**
     * Gives every model a text status depending on 'status_id' and 'is_visible' properties.
     *
     * @param mixed $model Array of objects or single object.
     * @return mixed
     */
    public static function determineStatusMessage($model)
    {
        if (is_object($model)) $model = [$model];

        foreach ($model as $item) {
            if ($item->status_id == Yii::$app->params['status']['accepted']) {
                $msg = ((bool)$item->is_visible) ? Yii::$app->params['statusMessages']['acceptedAndVisible'] : Yii::$app->params['statusMessages']['acceptedAndNotVisible'];
            } else {
                $msg = ((bool)$item->is_visible) ? Yii::$app->params['statusMessages']['notAcceptedAndVisible'] : Yii::$app->params['statusMessages']['notAcceptedAndNotVisible'];
            }
            $item->status_msg = $msg;
        }
    }

    /**
     * Creates link tail for returning browser to referer page and approximate position after accepting or rejecting user.
     *
     * @param string $anchor Anchor parameter from url.
     * @return string String with referer page and nearest user anchor.
     */
    public static function buildReturnLinkTail($anchor)
    {
        $strpos = strpos($anchor, '_');

        $page = ($strpos != false) ? '?page=' . substr($anchor, ++$strpos) : '';

        return $page . '#item_' . (int)$anchor;
    }

    /**
     * In case of Auth model save fail while registering, removes all current user tables' entries.
     *
     * @return void
     */
    public function userRegisterFailCleanup()
    {
        User::deleteAll(['id' => $this->id]);
        UserHasNormativeBases::deleteAll(['user_id' => $this->id]);
        UserHasSmetaDocs::deleteAll(['user_id' => $this->id]);
        UserHasProfessions::deleteAll(['user_id' => $this->id]);
    }

    /**
     * Sorts keys query result according to array_multisort arguments' order,
     * saves required variables in session and returns arrays for further data processing.
     *
     * @param array $result Array with results depending on main page filters.
     * @param array $qp Query string parameters.
     * @param array $staticDBsContent Array with static content.
     * @param integer $countQuery Number of items in keys query result.
     * @param integer $sortParams Sorting price direction for results.
     * @return array [
     *        'ids' => Array with ids in rearranged order according to array_multisort arguments.
     *        'matchesPercentArr' => Array with matched percents for every item.
     *        'related' => Array with junction tables data for every item.
     *        'cityIdArr' => Array with city_ids' of current visible and accepted users
     * ]
     */
    public static function processKeysQueryResult($result, $qp, $staticDBsContent, $countQuery, $sortParams)
    {
        $qp = Site::massExplode(['professions', 'normbases', 'smetadocs'], $qp);

        $ids = [];

        $idsId = 1;

        foreach ($result as $item) {
            $ids[$idsId++] = $item['id'];
        }

        if (Yii::$app->session->has('related') && ((int)$countQuery) === count(Yii::$app->session->get('related'))) {
            $result = User::countWeight($qp, $result, Yii::$app->session->get('related'));
            $related = null;
        } else {
            $related = User::prepareRelatedArrays($ids);
            $result = User::countWeight($qp, $result, $related);
        }

        $city_id = [];

        foreach ($result as $key => $row) {
            $weight[$key] = $row['weight'];
            $has_price[$key] = $row['has_price'];
            $price[$key] = $row['price'];
            $changed[$key] = (!empty($row['date_changed'])) ? $row['date_changed'] : $row['date_created'];

            if (!empty($row['city_id']) && !in_array($row['city_id'], $city_id)) $city_id[$row['city_id']] = (int)$row['city_id'];
        }

        $sortDirection = ($sortParams['direction'] == 'asc') ? SORT_ASC : SORT_DESC;

        if ($sortParams['filter'] == 'price') {
            array_multisort($weight, SORT_DESC, $has_price, SORT_DESC, $price, $sortDirection, $changed, SORT_DESC, $result);
        } elseif ($sortParams['filter'] == 'date') {
            array_multisort($weight, SORT_DESC, $changed, $sortDirection, $result);
        }

        $ids = [];

        foreach ($result as $item) {
            $ids[] = $item['id'];
            $matchesPercentArr[$item['id']] = $item['match_percent'];
        }

        User::saveToSession($ids, $related, $staticDBsContent, $matchesPercentArr, $countQuery);

        return [
            'ids' => $ids,
            'matchesPercentArr' => $matchesPercentArr,
            'related' => (!empty($related)) ? $related : null,
            'cityIdArr' => $city_id
        ];
    }

    /**
     * Saves "core" arrays to session.
     *
     * @param array $ids Array with rearranged keys, prepared for final query.
     * @param array $related Array with junction tables data for every item.
     * @param array $staticDBsContent Array with static content.
     * @param array $matchesPercentArr Array with matched percents for every item.
     * @param $countQuery Number of items in keys query result.
     * @return void
     */
    public static function saveToSession($ids, $related, $staticDBsContent, $matchesPercentArr, $countQuery)
    {
        Yii::$app->session->set('keys', implode(',', $ids));

        if (!Yii::$app->session->has('related') || Yii::$app->session->has('related') && (((int)$countQuery) !== count(Yii::$app->session->get('related')))) {
            Yii::$app->session->set('related', $related);
        }

        if (!Yii::$app->session->has('staticDBsContent')) Yii::$app->session->set('staticDBsContent', $staticDBsContent);

        Yii::$app->session->set('matchesPercentArr', $matchesPercentArr);

    }

    /**user@user.test
     * Walks through corresponding related array and concatenates values in row.
     *
     * @param $relatedArray
     * @param $staticArray
     * @param $qp
     * @return string
     */
    public static function printSelect2Items($relatedArray, $staticArray, $qp)
    {
        $str = '';
        $cnt = 0;

        foreach ($relatedArray as $prof) {

            $itemProf = $staticArray[$prof];

            $delimiter = (++$cnt != count($relatedArray)) ? '; ' : '';

            $str .= (in_array($itemProf['id'], $qp)) ?
                '<span class="match item">' .
                $itemProf['title'] . $delimiter .
                '</span>' : '<span class="default item">' .
                $itemProf['title'] . $delimiter . '</span>';
        }

        return $str;
    }

    /**
     * Determines percent matches rendering color.
     *
     * @param int $percent
     * @return string
     */
    public static function determinePercentColor($percent)
    {
        if ($percent > 69) {
            $class = 'green';
        } elseif ($percent < 70 && $percent > 29) {
            $class = 'yellow';
        } else {
            $class = 'red';
        }

        return $class;
    }

    public static function gatherReportsCount($id)
    {

        $currentDay = time();

        $arr = [
            7 => Report::countFilterGroup($id, 7, $currentDay),
            '30days' => Report::countFilterGroup($id, 30, $currentDay),
            'all' => Report::find()->where(['user_id' => $id])->count()
        ];

        return $arr;
    }

    /**
     * Renders correct, russian-style date
     * @param $arr array
     * @return string
     */
    public static function renderDate($arr)
    {
        $time = (!empty($arr['date_changed'])) ? $arr['date_changed'] : $arr['date_created'];
        return date('d ' . Site::escapeStringForDateFunc(Site::russianMonthNames(date('n', $time))) . ' Y \г\.', $time);
    }

    /**
     * Updates date_changed param if last update wasn't today.
     * @return void
     */
    public function checkLastChangedTime()
    {
        if (!empty($this->date_changed)) {
            $checkCurrentChangedTime = date('z.Y', $this->date_changed);
            $time = time();
            $currentTime = date('z.Y', $time);
            if ($checkCurrentChangedTime !== $currentTime) $this->date_changed = $time;
        }
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

    /**
     * Relation with Profession Model.
     */
    public function getProfessions()
    {
        return $this->hasMany(Profession::className(),
            ['id' => 'profession_id'])->viaTable('user_has_professions', ['user_id' => 'id']);
    }

    /**
     * Для нормального использования релейшена,
     * а то какой-то пьяный мастер зачем-то завязал все на свойства и получать для вывода $user->pro... невозможно
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProfessionsNorm()
    {
        return $this->hasMany(Profession::className(),
            ['id' => 'profession_id'])->viaTable('user_has_professions', ['user_id' => 'id']);
    }

    /**
     * Relation with SmetaDoc Model.
     */
    public function getSmetadocs()
    {
        return $this->hasMany(SmetaDoc::className(),
            ['id' => 'smeta_docs_id'])->viaTable('user_has_smeta_docs', ['user_id' => 'id']);
    }

    /**
     * Relation with NormBase Model.
     */
    public function getNormbases()
    {
        return $this->hasMany(NormBase::className(),
            ['id' => 'normative_bases_id'])->viaTable('user_has_normative_bases', ['user_id' => 'id']);
    }

    /**
     * Relation with Phone Model.
     */
    public function getPhones()
    {
        return $this->hasMany(Phone::className(), ['id' => 'phone_id'])
            ->viaTable('users_phones', ['user_id' => 'id'])
            ->innerJoin('users_phones', 'phones.id = users_phones.phone_id')
            ->orderBy(['users_phones.index' => SORT_ASC]);
    }

    public function getRealID()
    {
        if ($this->email == null) return null;

        /** @var Auth $auth */
        $auth = Auth::find()->where(['login' => $this->email])->one();
        return (isset($auth->id)) ? $auth->id : null;
    }

    public function getCity()
    {
        if (is_null($this->city_id)) {

            $cityTmp = new City();
            $cityTmp->name = "";
            $cityTmp->id = null;

            return $cityTmp;
        }

        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Метод возваращает массив заполенный заголовками значений из связанных с Юзером таблиц.
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

    public function getReal_id()
    {
        /** @var Auth $auth */
        $auth = Auth::findOne(['user_id' => $this->id]);

        if (!empty($auth)) {
            return $auth->id;
        }

        return null;
    }

    public function setReal_id($id)
    {
        if ($id !== null) {
            $this->real_id = $id;
        }
    }

    ////////////////////////

    public function __get($property)
    {
        if ($property === 'email') {
            return $this->email;
        } else {
            return parent::__get($property);
        }
    }

    public function __set($property, $value)
    {
        if ($property === 'email') {
            $this->email = $value;
        } else {
            parent::__set($property, $value);
        }
    }

}
