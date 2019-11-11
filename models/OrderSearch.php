<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $user_change_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $finished_at
 * @property string $name
 * @property string $fio
 * @property string $phone
 * @property string $email
 * @property integer $price
 * @property string $text
 * @property string $link
 * @property integer $published
 * @property integer $checked
 * @property integer $closing_reason
 * @property string $closing_reason_text
 *
 * @property string $searchText
 * @property integer $searchId
 * @property integer $searchStatus
 * @property integer $dataResultsTotal
 *
 * @property Profession[] $professions;
 * @property SmetaDoc[] $smetaDocs;
 * @property NormBase[] $normBases;
 */
class OrderSearch extends Order
{
    public $searchText;
    public $searchId;
    public $searchStatus;

    public $priceSort;

    public $dataResultsTotal;

    public $professionsId;
    public $normBasesId;
    public $smetaDocsId;

    public function rules()
    {
        // только поля определенные в rules() будут доступны для поиска
        return [
            [['id'], 'integer'],
            [['name', 'updated_at', 'email', 'phone', 'published', 'checked', 'professionsId', 'normBasesId', 'smetaDocsId'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Order::find()->with('auth')->with('customer');

        $query->where(['published' => 1]);

        if ((isset(Yii::$app->user->identity->is_admin)) && ((Yii::$app->user->identity->is_admin))) {
            $query->addOrderBy('`checked` asc');
            $query->addOrderBy('published desc');
            $query->addOrderBy("`updated_at` desc");
        }

        if (!empty($this->priceSort)) {

            if ($this->priceSort == 'desc') {
                $query->addOrderBy("`updated_at` desc");
            } elseif($this->priceSort == 'asc'){
                $query->addOrderBy("`updated_at` asc");
            }
        }

        if (isset(Yii::$app->user->identity->is_admin)) {
            if (Yii::$app->user->identity->is_admin) {
                $query->orWhere(['published' => 0]);
            }
        }

        $query->addOrderBy('updated_at DESC');

        if (!empty($this->searchText)) {

			$query->andFilterWhere(['or', ['like', 'name', $this->searchText], ['like', 'fio', $this->searchText], ['like', 'email', $this->searchText]]);

			$phone = Phone::find()->where([
				'like',
				'number',
				$this->searchText
			])->indexBy('id')->all();

			if(!empty($phone)){
				$query->orFilterWhere(['in', 'id', (new Query())->select('order_id')->from('order_phones')->where(['in' ,'phone_id', array_keys($phone)])]);
			}

        }

        if (!empty($this->searchId)) {
            $query->andFilterWhere(['=', 'id', $this->searchId]);
        }

        if (!empty($this->searchStatus)) {

            switch ($this->searchStatus) {
                case '0':
                    break;
                case '1':
                    $query->andFilterWhere(['=', 'published', '1']);
                    $query->andFilterWhere(['=', 'checked', '0']);
                    break;
                case '2':
                    $query->andFilterWhere(['=', 'published', '1']);
                    $query->andFilterWhere(['=', 'checked', '1']);
                    break;
                case '3':
                    $query->andFilterWhere(['=', 'published', '0']);
                    break;
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->dataResultsTotal * 7,
            ],
        ]);

        // загружаем данные формы поиска и производим валидацию
        if (!($this->load($params) && $this->validate())) {

            if (!empty($this->professionsId)) {

                $query->joinWith('professions');
                $query->andWhere(['IN', 'professions.id', $this->professionsId]);
            }

            if (!empty($this->normBasesId)) {

                $query->joinWith('normBases');
                $query->andWhere(['IN', 'normative_bases.id', $this->normBasesId]);
            }

            if (!empty($this->smetaDocsId)) {

                $query->joinWith('smetaDocs');
                $query->andWhere(['IN', 'smeta_docs.id', $this->smetaDocsId]);

            }

            return $dataProvider;
        }

        $query->groupBy('id');

        return $dataProvider;
    }
}