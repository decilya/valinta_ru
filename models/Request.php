<?php

namespace app\models;

use app\components\validators\CustomEmailValidator;

/**
 * This is the model class for table "requests".
 *
 *
 * @property integer $id
 * @property string $fio
 * @property string $email
 * @property string $phone
 * @property integer $date_created
 * @property integer $status_value
 * @property string $comment
 *
 * @property Database[] $databases;
 * @property string $inn
 * @property integer $access_days
 * @property integer $cost
 * @property string $text
 * @property integer $desired_date
 *
 * @property array $databasesTmp
 *
 */
class Request extends \yii\db\ActiveRecord
{
    public $request_agreement;
    public $databasesTmp;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'email', 'phone', 'date_created', 'request_agreement', 'databasesTmp', 'desired_date'], 'required', 'message' => 'Это обязательное поле'],

            [['phone'], 'match', 'pattern' => '/^79|^\+7\(9/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'match', 'pattern' => '/^((?!_).)*$/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'string', 'min' => 11, 'tooShort' => 'Некорректный номер'],

            [['fio'], 'string', 'max' => 100, 'tooLong' => 'Поле должно содержать не более 100 символов'],
            ['email', 'string', 'max' => 50, 'tooLong' => 'Поле должно содержать не более 50 символов'],
            [['phone'], 'string', 'max' => 20],

            ['phone', 'match', 'pattern' => '/^79|^\+7\(9/', 'message' => 'Пожалуйста, введите номер мобильного телефона'],
            ['phone', 'string', 'min' => 11, 'tooShort' => 'Некорректный номер'],

            ['email', CustomEmailValidator::className(), 'message' => 'Некорректный адрес'],
            ['email', 'trim'],

            ['request_agreement', 'compare', 'compareValue' => 1, 'message' => 'Необходимо согласиться с условиями предоставления ПП SmetaWIZARD.'],

            [['date_created', 'status_value', 'access_days', 'cost'], 'integer', 'message' => 'Введите целое число'],
            [['inn'], 'innValid'],
            [['comment'], 'string'],
            [['fio'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['comment'], 'string', 'tooLong' => 'Максимум 500 символов', 'max' => 500],
            [['access_days'], 'accessDays'],
            [['desired_date'], 'desiredDateValid']


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inn' => 'ИНН',
            'fio' => 'Ф.И.О.',
            'email' => 'Адрес электронной почты',
            'phone' => 'Мобильный телефон',
            'date_created' => 'Date Created',
            'status_value' => 'Status Value',
            'access_days' => 'Количество дней доступа',
            'cost' => 'Стоимость заказа',
            'comment' => 'Если нужные Вам базы отсутствуют в списке, напишите здесь',
            'desired_date' => 'Желаемая дата доступа',
            'databasesTmp' => 'Нормативные базы',
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function innValid($attribute, $params)
    {
        if (strlen($this->inn) != 12) {
            $this->addError($attribute, '12 символов, только цифры');
        }

        // true если все символы цифры
        if (!ctype_digit($this->inn)) {
            $this->addError($attribute, '12 символов, только цифры');
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function accessDays($attribute, $params)
    {
        if (($this->access_days < 1) || ($this->access_days > 30)) {
            $this->addError($attribute, 'от 1 до 30, только цифры');
        }

        if (!ctype_digit($this->access_days)) {
            $this->addError($attribute, 'от 1 до 30, только цифры');
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function desiredDateValid($attribute, $params)
    {
        $time = strtotime($this->desired_date);

        $time2 = strtotime(date('d.m.Y'));

        if ((!isset($this->desired_date[2])) || (!isset($this->desired_date[5]))) {
            $this->addError($attribute, 'Неверный формат даты');
        }

        if ((($this->desired_date[2] !== '.')) || (($this->desired_date[5] !== '.'))) {
            $this->addError($attribute, 'Неверный формат даты');
        }

        if ((strlen($this->desired_date)) != 10) {
            $this->addError($attribute, 'Неверный формат даты');
        }

        if ((!is_numeric($this->desired_date[0])) ||
            (!is_numeric($this->desired_date[1])) ||
            (!is_numeric($this->desired_date[3])) ||
            (!is_numeric($this->desired_date[4])) ||
            (!is_numeric($this->desired_date[6])) ||
            (!is_numeric($this->desired_date[7])) ||
            (!is_numeric($this->desired_date[8])) ||
            (!is_numeric($this->desired_date[9]))
        ) {
            $this->addError($attribute, 'Неверный формат даты');
        }

        if ($time < $time2) {
            $this->addError($attribute, 'Дата в прошлом');
        }
    }

    public function afterValidate()
    {
        parent::afterValidate();
        $this->desired_date = strtotime($this->desired_date);

        if ($this->access_days == null) $this->access_days = 1;
    }

    /**
     * @return false|string
     */
    public function getDesiredDay()
    {
        return date('d-m-Y', $this->desired_date);
    }

    /**
     * Для нормального использования релейшена со списком ДБ
     *
     * @return \yii\db\ActiveQuery
     */

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatabases()
    {
        return $this->hasMany(Database::className(),
            ['id' => 'database_id'])->viaTable('requests_has_database', ['request_id' => 'id']);
    }


    /**
     * @param $arrId
     */
    public function setDatabases($arrId)
    {
        $tmpRecords = RequestsHasDatabase::find()->where(['request_id' => $this->id])->each();
        foreach ($tmpRecords as $item) {
            $item->delete();
        }

        foreach ($arrId as $item) {

            /**
             * @var RequestsHasDatabase $tmpRecord
             */
            $tmpRecord = new RequestsHasDatabase();

            $tmpRecord->request_id = $this->id;
            $tmpRecord->database_id = $item;

            $tmpRecord->save();
        }

        $this->databases = $arrId;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $this->fio = strip_tags($this->fio);
            $this->email = strip_tags($this->email);

            return true;
        }

        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (is_array($this->databasesTmp)) {
            $this->setDatabases($this->databasesTmp);
        }
    }

    /**
     * This method corrects subject of e-mail message.
     *
     * @param string $subj Initial subject.
     * @param integer $id Id of current request.
     * @return string Updated subject.
     */
    public function requestMessageSubjWithSuffix($subj, $id)
    {
        return $subj . ' №' . $id . ' НА ПП SMETAWIZARD';
    }

    public function getDatabasesId()
    {
        $tmpArr = [];
        foreach ($this->databases as $database) {
            $tmpArr[] = $database['id'];
        }

        return $tmpArr;
    }

}
