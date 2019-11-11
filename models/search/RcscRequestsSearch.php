<?php

namespace app\models\search;

use app\models\Customer;
use app\models\CustomerPhone;
use app\models\Phone;
use app\models\Request;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\db\Query;
use Ds\Set;


/**
 * UsesSearch represents the model behind the search form about `app\models\User`.
 *
 * @property string $start_at
 * @property string $finish_at
 */
class RcscRequestsSearch extends Customer
{
    public $desired_date;
    public $start_at;
    public $finish_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['desired_date', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'desired_date' => 'Оформлена',
            'start_at' => '',
            'finish_at' => ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        if ($params['databasesIds']) {

            $this->load($params);

            $query =  Request::find();

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    // 'totalCount' => $query->count(),
                    'pageSize' => Yii::$app->params['itemsOnUserIndexPage'],
                    //   'defaultPageSize' => Yii::$app->params['itemsOnUserIndexPage'],
                ],
            ]);

            if (!empty($params['databasesIds'])) {
                $query->innerJoin('requests_has_database', '`requests_has_database`.`request_id` = `requests`.`id`');
                $query->where(['in', 'requests_has_database.database_id', $params['databasesIds']]);
            }

            if (!empty($params['searchId'])) {
                $query->andFilterWhere(['requests.id' => (int)$params['searchId']]);
            }

            if ((!empty($params['start_at'])) && (!empty($params['finish_at']))) {

                $startAt = strtotime($params['start_at']);
                $finishAt = strtotime($params['finish_at']);

                $query->andFilterWhere(['>', 'date_created', $startAt]);
                $query->andFilterWhere(['<', 'date_created', $finishAt]);
            }

            $query->groupBy('requests_has_database.request_id');
            $query->orderBy(['requests.id' => SORT_DESC]);
            $query->all();

            return $dataProvider;
        } else {
            return null;
        }
    }
}