<?php

namespace app\models;

use app\components\validators\CustomEmailValidator;
use app\models\traits\SetCreatedAtTrait;
use Yii;

/**
 * This is the model class for table "rcsc".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property int $status_id
 * @property int $created_at
 * @property int $real_id
 *
 * @property Database[] $databases
 *
 */
class Rcsc extends \yii\db\ActiveRecord
{
    use SetCreatedAtTrait;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    const STATUS_REQUIRES_VERIFICATION = [
        'val' => 1,
        'title' => 'Требует проверки'
    ];
    const STATUS_CONFIRMED = [
        'val' => 2,
        'title' => 'Подтверждён'
    ];
    const STATUS_REJECTED = [
        'val' => 3,
        'title' => 'Отклонён',
    ];

    private $email;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rcsc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'name'], 'required'],
            [['status_id', 'created_at', 'real_id'], 'integer'],

            ['email', 'validateRealUniqueEmail', 'on' => self::SCENARIO_REGISTER],
            ['email', 'validateRealUniqueEmailUpdate', 'on' => self::SCENARIO_UPDATE],

            ['email', CustomEmailValidator::className(), 'message' => 'Некорректный адрес'],
            [['email', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatabases()
    {
        return $this->hasMany(Database::className(),
            ['id' => 'database_id'])->viaTable('rcsc_has_database', ['rcsc_id' => 'id']);
    }


    public function afterFind()
    {
        parent::afterFind();

        if ($this->id != null) {
            /** @var Auth $auth */
            $auth = Auth::findOne(['rcsc_id' => $this->id]);

            if (isset($auth->login)) {
                $this->email = $auth->login;
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmail($attribute, $params)
    {
        if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
            $this->addError($attribute, "E-mail уже зарегистрирован");
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmailUpdate($attribute, $params)
    {
        if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
            $this->addError($attribute, "E-mail уже зарегистрирован");
        }

        $patter = "|[а-яё]|is";
        if (preg_match($patter, $this->email)) {
            $this->addError($attribute, "Кириллические символы недопустимы в email");
        }
    }



    /**
     * @param $arrId
     */
    public function setDatabases($arrId)
    {
        $tmpRecords = RcscHasDatabase::find()->where(['rcsc_id' => $this->id])->each();
        foreach ($tmpRecords as $item) {
            $item->delete();
        }

        foreach ($arrId as $item) {

            /**
             * @var RcscHasDatabase $tmpRecord
             */
            $tmpRecord = new RcscHasDatabase();

            $tmpRecord->rcsc_id = $this->id;
            $tmpRecord->database_id = $item;

            $tmpRecord->save();
        }

        $this->databases = $arrId;
    }


    public function getReal_id()
    {
        /** @var Auth $auth */
        $auth = Auth::findOne(['rcsc_id' => $this->id]);

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
}