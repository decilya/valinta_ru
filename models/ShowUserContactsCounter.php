<?php

namespace app\models;

use app\models\traits\SetCreatedAtTrait;
use Yii;
use \yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

/**
 * This is the model class for table "show_user_contacts_counter".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $user_id
 * @property integer $created_at
 *
 * @property int $dayStart
 * @property int $dayFinish
 */
class ShowUserContactsCounter extends ActiveRecord
{
    use SetCreatedAtTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'show_user_contacts_counter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'user_id', 'created_at'], 'required'],
            [['customer_id', 'user_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return int
     */
    public static function getDayStart()
    {
        return mktime(0, 0, 0);
    }

    /**
     * @return int
     */
    public static function getDayFinish()
    {
        return self::getDayStart() + 86400;
    }

    public static function getCustomerCounterForDay($customer_id)
    {
        return self::find()
            ->where(['customer_id' => $customer_id])
            ->andWhere(['>', 'created_at', self::getDayStart()])
            ->andWhere(['<', 'created_at', self::getDayFinish()])
            ->count();
    }

    public static function getCustomerCounterForDayByAuthId($auth_id)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $auth_id])->one();

        /** @var Customer $customer */
        $customer = Customer::findOne(['id' => $auth->customer_id]);

        if (empty($customer)) return null; /// throw new  BadRequestHttpException('Искомый сметчик не обнаружен');

        return self::find()
            ->where(['customer_id' => $customer->id])
            ->andWhere(['>', 'created_at', self::getDayStart()])
            ->andWhere(['<', 'created_at', self::getDayFinish()])
            ->count();
    }

    /**
     * Увеличить счетик на один
     *
     * @param $customer_id
     * @param $user_id
     * @return bool
     */
    public static function incCounter($customer_id, $user_id)
    {
        if (Auth::getUserType() !== Auth::TYPE_CUSTOMER) return false;

        $counterToday = self::find()
            ->where(['customer_id' => $customer_id])
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['>', 'created_at', self::getDayStart()])
            ->andWhere(['<', 'created_at', self::getDayFinish()])
            ->count();

        if ($counterToday == 0) {
            /** @var ShowUserContactsCounter $counter */
            $counter = new self();
            $counter->customer_id = $customer_id;
            $counter->user_id = $user_id;

            return $counter->save();
        }

        return false;
    }

    public static function residualTime()
    {
        return self::getDayFinish() - time();
    }


}