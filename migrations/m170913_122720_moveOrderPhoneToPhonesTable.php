<?php

use app\models\Order;
use app\models\Phone;
use app\models\User;
use yii\db\Migration;

class m170913_122720_moveOrderPhoneToPhonesTable extends Migration
{
	public function safeUp()
	{

		$orders = Order::find()->select('id, phone')->all();

		foreach ($orders as $order) {
			$phone = Phone::find()->where([
				'number' => $order->phone
			])->one();

			if(empty($phone)){
				$phone = new Phone([
					'number' => $order->phone,
				]);

				if(!$phone->save()) return false;
			}

			$order->link('phones', $phone, [
				'index' => 1,
				'is_new' => 0
			]);
		}

		$this->dropColumn('order', 'phone');
	}

	public function safeDown()
	{
		$this->addColumn('order', 'phone', 'string NOT NULL');

		$phone = Phone::find()->where([
		])->indexBy('id')->all();

		$q = Yii::$app->db->createCommand("SELECT * FROM order_phones WHERE `is_new` = 0 AND `index` = 1;")->queryAll();

		foreach ($q as $item) {
			$order = Order::findOne($item['order_id']);
//			$order->phone = $phone[$item['phone_id']]->number;
			if(Yii::$app->db->createCommand("UPDATE `order` SET `phone` = '".$phone[$item['phone_id']]->number."' WHERE `id` = ".$order->id)->execute()){
//			if($order->save(false, ['phone'])){
				$order->unlink('phones', $phone[$item['phone_id']], true);
			};

			$checkUserPhones = Yii::$app->db->createCommand("SELECT * FROM users_phones WHERE `phone_id` = ".$item['phone_id'])->queryAll();
			$checkOrderPhones = Yii::$app->db->createCommand("SELECT * FROM order_phones WHERE `phone_id` = ".$item['phone_id'])->queryAll();

			if(empty($checkUserPhones) && empty($checkOrderPhones)) $phone[$item['phone_id']]->delete();
		}
	}
}
