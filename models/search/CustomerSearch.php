<?php

namespace app\models\search;

use app\models\Auth;
use app\models\Customer;
use app\models\CustomerPhone;
use app\models\Phone;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\db\Exception;
use yii\db\Query;
use Ds\Set;


/**
 * UsesSearch represents the model behind the search form about `app\models\User`.
 */
class CustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['name', 'created_at', 'phone', 'email', 'updated_at', 'real_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function search($params)
    {
        $query = Customer::find()->with('customerPhones');
        $query->innerJoin('auth', '`auth`.`customer_id` = `customer`.`id`');

        $this->load($params);

        if (!$this->validate()) {
            goto finalAction;
        }

        if (!empty($params['id'])) {
            /** @var Auth $auth */
            $auth = Auth::find()->where(['id' => (int)$params['id']])->one();
            $query->andFilterWhere(['auth.id' => $params['id']]);
        }

        if (!empty($params['text'])) {

            $text = (!empty($params['text']) ? strip_tags(trim($params['text'])) : "");
            $phones = CustomerPhone::find()->where(['like', 'phone', $text])->indexBy('id')->asArray()->all();

            $tmp = (new Query())->select('customer_id')
                ->from('customer_phone')
                ->where(['in', 'phone', $phones])
                ->all();

            $customerIds = [];
            foreach ($tmp as $i) {
                $customerIds[] = $i['customer_id'];
            }

            $customerIds = array_values(array_unique($customerIds));

            if (!empty($customerIds)) {

                $query->orFilterWhere(['in', 'id', $customerIds]);

            } else {
                $query->orFilterWhere(['or', ['like', 'name', $text]]);
                $query->orFilterWhere(['or', ['like', 'auth.login',  $text]]);
            }
        }

        if (!empty($params['userStatus'])) {
            $query->andFilterWhere(['status_id' => (int)$params['userStatus']]);
        }

        if (!empty($params['status'])) {
            $query->andFilterWhere(['status_id' => (int)$params['status']]);
        }

        finalAction:
        if ((!isset($params['sort']) || $params['sort'] == null) || ($params['sort'] == 0)) {
            $query->orderBy([new \yii\db\Expression("FIELD(status_id, '1', '3', '2')")])->addOrderBy(['updated_at' => SORT_ASC]);
        } elseif ($params['sort'] == 1) {
            $query->orderBy(['auth.id' => SORT_DESC]);
        } elseif ($params['sort'] == 2) {
            $query->orderBy(['auth.id' => SORT_ASC]);
        } else {
            throw new Exception('Не верно задан параметр сортировки.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'totalCount' => $query->count(),
                // 'pageSize' => Yii::$app->params['itemsOnUserIndexPage'],
                'pageSize' => 7,
                'defaultPageSize' => Yii::$app->params['itemsOnUserIndexPage'],
            ],
        ]);

        return $dataProvider;
    }
}