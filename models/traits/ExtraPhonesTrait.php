<?php

namespace app\models\traits;

use app\models\Phone;
use app\models\User;
use app\models\UsersPhones;
use Yii;

trait ExtraPhonesTrait
{

    /**
     * Saves phone numbers after registration.
     * @return void
     */
    public function savePhoneNumbers()
    {

        $phoneArr = [
            1 => $this->phone
        ];

        if (!empty($this->extraPhones) && count($this->extraPhones) > 0) {
            foreach ($this->extraPhones as $k => $v) if (empty($this->extraPhones[$k])) unset($this->extraPhones[$k]);
            $phoneArr += $this->extraPhones;
        }

        $cnt = 1;

        foreach ($phoneArr as $key => $number) {
            $phone = Phone::find()->where([
                'number' => $number
            ])->one();

            if (empty($phone)) {
                $phone = new Phone([
                    'number' => $number
                ]);
                $phone->save();
            }

            if ($this instanceof User) {

                /** @var UsersPhones $userPhone */
                $userPhone = new UsersPhones();
                /** @var User $this */
                $userPhone->user_id = $this->id;
                $userPhone->phone_id = $phone->id;
                $userPhone->index = $cnt;
                $userPhone->is_new = 1;
                $userPhone->save();

            } else {
                $this->link('phones', $phone, [
                    'index' => $cnt,
                    'is_new' => 1
                ]);
            }

            $cnt++;
        }
    }


    /**
     * @param $tableName
     * @param $fieldName
     * @throws \yii\db\Exception
     */
    public function fetchPhoneNumbers($tableName, $fieldName)
    {
        $phones = [];

        $cmd = Yii::$app->db->createCommand('SELECT * FROM ' . $tableName . ' WHERE ' . $fieldName . '=:id ORDER BY `index`');
        $cmd->bindValue(':id', $this->id);

        $bindings = $cmd->queryAll();

        foreach ($bindings as $item) {
            $phones[] = Phone::find()->where([
                'id' => $item['phone_id']
            ])->one();
        }

        foreach ($phones as $key => $phone) {
            if ($key == 0) {
                $this->phone = $phone->number;
            } else {
                $this->extraPhones[($key + 1)] = $phone->number;
            }
        }
    }

    /**
     * Method for checking if phone numbers were changed.
     * @return void
     */
    public function checkDirtyPhoneNumbers($oldModel)
    {

        $this->phoneArr = [
            1 => $this->phone
        ];


        if ($this->extraPhones !== null && count($this->extraPhones) > 0) {
            $extraPhonesArr = [];
            $cnt = 2;
            foreach ($this->extraPhones as $k => $v) {
                if (!empty($this->extraPhones[$k])) {
                    $extraPhonesArr[$cnt] = $v;
                    $cnt++;
                }
            }
            $this->phoneArr += $extraPhonesArr;
        }

        $oldPhoneArr = [
            1 => $oldModel->phone
        ];

        if ($oldModel->extraPhones !== null && count($oldModel->extraPhones) > 0) {
            $oldPhoneArr += $oldModel->extraPhones;
        }

        if (!empty(array_diff($this->phoneArr, $oldPhoneArr)) || !empty(array_diff($oldPhoneArr, $this->phoneArr))) $this->dirtyPhoneNumbers = true;

    }

    /**
     * Method for updating phone numbers' change.
     *
     * @param $oldModel
     * @param $tableName
     * @param $fieldName
     * @throws \yii\db\Exception
     */
    public function manageDirtyPhoneNumbers($oldModel, $tableName, $fieldName)
    {

        if (!empty($this->extraPhones)) {
            $cnt = 2;
            $extraPhones = [];
            foreach ($this->extraPhones as $k => $v) {
                if (!empty($this->extraPhones[$k])) $extraPhones[$cnt] = $v;
            }
        }

        $oldExtraPhonesCount = !empty($oldModel->extraPhones) ? count($oldModel->extraPhones) : 0;
        $newExtraPhonesCount = !empty($extraPhones) ? count($extraPhones) : 0;

        if ($oldExtraPhonesCount > $newExtraPhonesCount && !empty($oldModel->extraPhones)) {
            foreach ($oldModel->extraPhones as $oldKey => $oldNumber) {
                if (!array_key_exists($oldKey, $this->phoneArr)) $this->phoneArr[$oldKey] = '';
            }
        }

        foreach ($this->phoneArr as $key => $number) {

            if ($key == 1) {
                $attrVal = $oldModel->phone;
            } elseif (!empty($oldModel->extraPhones[$key])) {
                $attrVal = $oldModel->extraPhones[$key];
            } else {
                $attrVal = '';
            }

            if ($number != $attrVal) {
                if (!empty($attrVal)) {
                    $cmd = Yii::$app->db->createCommand('DELETE FROM ' . $tableName . ' WHERE ' . $fieldName . '=:id AND `index`=:index');
                    $cmd->bindValues([
                        ':id' => $this->id,
                        ':index' => $key
                    ]);
                    $cmd->execute();
                }

                if (!empty($number)) {
                    $phone = Phone::find()->where([
                        'number' => $number
                    ])->one();

                    if (empty($phone)) {
                        $phone = new Phone([
                            'number' => $number
                        ]);
                        $phone->save();
                    }

                    $this->link('phones', $phone, [
                        'index' => $key,
                        'is_new' => 1
                    ]);
                }
            }
        }
    }

}