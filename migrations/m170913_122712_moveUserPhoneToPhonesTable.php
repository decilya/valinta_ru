<?php

use app\models\Phone;
use app\models\Site;
use yii\db\Migration;
use app\models\User;

class m170913_122712_moveUserPhoneToPhonesTable extends Migration
{
    public function safeUp()
    {

		$users = User::find()->select('id, phone')->all();

		foreach ($users as $user) {
			$phone = Phone::find()->where([
				'number' => $user->phone
			])->one();

			if(empty($phone)){
				$phone = new Phone([
					'number' => $user->phone,
				]);

				if(!$phone->save()) return false;
			}

			$user->link('phones', $phone, [
				'index' => 1,
				'is_new' => 0
			]);
		}

		$this->dropColumn('users', 'phone');
	}

    public function safeDown()
    {
		$this->addColumn('users', 'phone', 'string(20) DEFAULT NULL');

		$phone = Phone::find()->where([
		])->indexBy('id')->all();

		$q = Yii::$app->db->createCommand("SELECT * FROM users_phones WHERE `is_new` = 0 AND `index` = 1;")->queryAll();

		foreach ($q as $item) {
			$user = User::findOne($item['user_id']);
			$user->scenario = USER::SCENARIO_UPDATE;
			$user->phone = $phone[$item['phone_id']]->number;
			if($user->save(true, ['phone'])){
				$user->unlink('phones', $phone[$item['phone_id']], true);
			};

			$checkUserPhones = Yii::$app->db->createCommand("SELECT * FROM users_phones WHERE `phone_id` = ".$item['phone_id'])->queryAll();
			$checkOrderPhones = Yii::$app->db->createCommand("SELECT * FROM order_phones WHERE `phone_id` = ".$item['phone_id'])->queryAll();

			if(empty($checkUserPhones) && empty($checkOrderPhones)) $phone[$item['phone_id']]->delete();
		}
	}
}
