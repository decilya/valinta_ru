<?php

namespace app\commands;

use app\models\Auth;
use app\models\Customer;
use app\models\Database;
use app\models\Rcsc;
use app\models\RcscHasDatabase;
use mysql_xdevapi\DocResult;
use Seld\CliPrompt\CliPrompt;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\gii\components\DiffRendererHtmlInline;
use yii\helpers\Console;


class RcscController extends ConsoleController
{
    /**
     * Метод добавляет пользователя. На вход принимает string $email, string $name.
     *
     * @param string $email
     * @param string $name
     * @return int
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 22.08.2018
     */
    public function actionAdd(string $email, string $name): int
    {
        /** @var Rcsc $rcscUser */
        $rcscUser = new Rcsc();
        $rcscUser->scenario = Rcsc::SCENARIO_REGISTER;

        $rcscUser->email = $email;
        $rcscUser->name = $name;

        if (!$rcscUser->save()) {

            echo "\n";
            print_r($rcscUser->errors);
            echo "\n";

            return ExitCode::UNSPECIFIED_ERROR;

        } else {

            $rcscUser->refresh();
            $password = Yii::$app->getSecurity()->generateRandomString(13);
            $password = 'Z' . $password . '7';

            if (Yii::$app->db->createCommand()->insert('auth',
                [
                    'login' => $email,
                    'password' => Yii::$app->getSecurity()->generatePasswordHash($password),
                    'is_admin' => 0,
                    'is_user' => 1,
                    'customer_id' => null,
                    'user_id' => null,
                    'recovery_token' => null,
                    'last_auth' => null,
                    'rcsc_id' => $rcscUser->id
                ]
            )->execute()) {

                $auth = Auth::findOne(['login' => $email]);

                $rcscUser = Rcsc::findOne(['id' => $auth->rcsc_id]);
                $rcscUser->scenario = Rcsc::SCENARIO_UPDATE;

                $rcscUser->real_id = $auth->id;
                if (!$rcscUser->save()) {

                    echo "\n";
                    print_r($rcscUser->errors);
                    echo "\n";

                    return ExitCode::UNSPECIFIED_ERROR;
                }

            }

            $this->printColorStr('Пользователь успешно сохранен', self::COLOR_SUCCESS);
            $this->printColorStr('Пароль:' . $password, self::COLOR_SUCCESS);

            return ExitCode::OK;
        }
    }

    /**
     * Метод выводит список всех пользователей РЦЦС, точнее их  id,  email и name (красным цветом выводятся отклоненные пользователи)
     *
     * @return int
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 23.08.2018
     */
    public function actionList(): int
    {
        /** @var Rcsc $list */
        $list = Rcsc::find()->all();

        for ($i = 0; $i < count($list); $i++) {

            $codeConsoleColor = ($list[$i]->status_id === Rcsc::STATUS_REJECTED['val'])
                ? Console::FG_RED
                : Console::FG_GREEN;

            $msg = ($list[$i]->status_id === Rcsc::STATUS_REJECTED['val'])
                ? ("$i) " . $list[$i]->id . " " . $list[$i]->email . " " . $list[$i]->name . " -- удаленный пользователь \n")
                : ("$i) " . $list[$i]->id . " " . $list[$i]->email . " " . $list[$i]->name . "\n");

            $this->stdout(
                $msg,
                $codeConsoleColor,
                Console::ITALIC
            );
        }

        return ExitCode::OK;
    }

    /**
     * Измнение записи пользователя РЦЦС, через пробел после имени метода - id пользователя, НОВЫЙ email, НОВОЕ имя, если параметр изменять не требуется, то следует указать старое значение.
     *
     * @param int $id
     * @param string $email
     * @param string $name
     * @return int
     * @throws \yii\db\Exception
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 23.08.2018
     */
    public function actionUpdate(int $id, string $email, string $name): int
    {
        /** @var Rcsc $item */
        $item = Rcsc::find()->where(['id' => $id])->with('databases')->one();

        if (empty($item)) {
            $this->stdout("Пользователь не найден.\n", Console::FG_RED, Console::NORMAL);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        /** @var Rcsc $oldItem */
        $oldItem = clone $item;

        $item->email = $email;
        $item->name = $name;

        // Если изменено только имя, то изменять login в Auth не требуется
        if ($oldItem->email === $item->email) {
            return $this->validAndSaveRcsc($item);
        } else {
            // Если все ок и сохранили, то давай изменим и auth
            if ($this->validAndSaveRcsc($item) === ExitCode::OK) {
                $item->refresh();

                if (Yii::$app->db
                    ->createCommand("UPDATE `auth` SET `login`='" . $email . "' WHERE `rcsc_id`='" . $item->id . "'")
                    ->execute()) {

                    $this->stdout(
                        "Логин успешно изменен!\n",
                        Console::FG_GREEN,
                        Console::NORMAL
                    );

                } else {
                    return ExitCode::UNSPECIFIED_ERROR;
                }
            }
        }

        return ExitCode::OK;
    }

    /**
     * Сохроняет модель Rcsc или выводит в консоль ошибки, возвращает ExitCode
     *
     * @param Rcsc $userRcsc
     * @return int
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 23.08.2018
     */
    public function validAndSaveRcsc(Rcsc $userRcsc): int
    {
        if (!$userRcsc->save()) {
            foreach ($userRcsc->errors as $key => $error) {
                $this->stdout("Ошибка в поле $key: $error[0]\n", Console::FG_RED, Console::NORMAL);
            }

            (count($userRcsc->errors) > 1) ?
                $this->stdout(
                    "Попробуйте изменить запись снова, исправив указанные ошибки.\n",
                    Console::FG_RED,
                    Console::BOLD
                ) :
                $this->stdout(
                    "Попробуйте изменить запись снова, исправив указанную ошибку.\n",
                    Console::FG_RED,
                    Console::BOLD
                );

            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    /**
     * Удаляет  пользователя из системы. На вход принимает id пользователя.
     *
     * @param int $id
     * @return int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 26.08.2018
     */
    public function actionDelete(int $id): int
    {
        $rcscUser = Rcsc::findOne(['id' => $id]);

        if (!$rcscUser) {
            $this->stdout(
                "Пользователь не найден.\n",
                Console::FG_RED,
                Console::BOLD
            );

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout(
            "Вы уверены, что хотите удалить пользователя " . $rcscUser->name . " " . $rcscUser->email . "? (y/n): ",
            Console::FG_GREEN,
            Console::NORMAL
        );
        $answer = CliPrompt::prompt();

        if ($answer === 'y') {

            $rcscUser->status_id = Rcsc::STATUS_REJECTED['val'];

            if (!$rcscUser->save()) {
                $this->stdout(
                    "Не удалось удалить пользователя",
                    Console::FG_RED,
                    Console::BOLD
                );
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $rcscHasDatabases = RcscHasDatabase::find()
                ->where(['rcsc_id' => $rcscUser->id])
                ->all();

            foreach ($rcscHasDatabases as $rcscHasDatabase) {
                $rcscHasDatabase->delete();
            }

            $this->stdout(
                "Пользователь успешно удален!\n",
                Console::FG_GREEN,
                Console::NORMAL
            );

        }

        return ExitCode::OK;
    }

    /**
     * Изменяет пароль пользователя. На выход принимает id и новый пароль.
     *
     * @param int $id
     * @param string $password
     * @return int
     * @throws \yii\base\Exception
     *
     * @author Ilya <ilya.v87v@gmail.com>
     * @data 26.08.2018
     */
    public function actionPassword(int $id, string $password): int
    {
        /** @var Rcsc $rcscUser */
        $rcscUser = Rcsc::findOne(['id' => $id]);

        if (!$rcscUser) {
            $this->stdout(
                "Пользователь не найден.\n",
                Console::FG_RED,
                Console::BOLD
            );

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $newPassword = Yii::$app->getSecurity()->generatePasswordHash($password);

        if (Yii::$app->db
            ->createCommand("UPDATE `auth` SET `password`='" . $newPassword . "' WHERE `rcsc_id`='" . $rcscUser->id . "'")
            ->execute()) {

            $this->stdout(
                "Пароль успешно изменен!\n",
                Console::FG_GREEN,
                Console::NORMAL
            );

            return ExitCode::OK;
        }

        return ExitCode::UNSPECIFIED_ERROR;

    }

    /**
     * Выводит список баз. Выводит: порядковый номер, id БД, название.
     */
    public function actionBasesList(): int
    {
        $list = Database::find()->all();

        /** @var Database $item */
        $i = 0;
        foreach ($list as $item) {
            $i++;
            $this->stdout(
                "$i) " . $item->id . " " . $item->name . "\n",
                Console::FG_GREEN,
                Console::ITALIC
            );
        }

        return ExitCode::OK;
    }

    /**
     * Выводит список баз относящихся к пользователю. Выводит:  порядковый номер, id БД, название.
     */
    public function actionUserBases(int $userId): int
    {
        /** @var Rcsc $rcsc */
        $rcsc = Rcsc::find()->where(['id' => $userId])->with('databases')->one();

        /** @var Database $item */
        $i = 0;
        if (!empty($rcsc->databases)) {
            foreach ($rcsc->databases as $item) {
                $i++;
                $this->stdout(
                    "$i) " . $item->id . " " . $item->name . "\n",
                    Console::FG_GREEN,
                    Console::ITALIC
                );
            }
        } else {
            $this->stdout(
                "Пользователю еще не добавлены базы данных!\n",
                Console::FG_RED,
                Console::BOLD
            );
        }


        return ExitCode::OK;
    }

    /**
     * Добавляет Пользователю РЦЦС ($userId) Базу ($baseId). На вход принимает: int $userId, int $baseId
     *
     * @param $userId
     * @param $baseId
     * @return int
     */
    public function actionUserBaseAdd(int $userId, int $baseId): int
    {
        // Проверим, что такой БД еще нет;
        $rcscHasDatabase = RcscHasDatabase::find()
            ->where(['rcsc_id' => $userId])
            ->andWhere(['database_id' => $baseId])
            ->one();

        if (empty($rcscHasDatabase)) {
            $rcscHasDatabase = new RcscHasDatabase();
            $rcscHasDatabase->rcsc_id = $userId;
            $rcscHasDatabase->database_id = $baseId;
            $rcscHasDatabase->created_at = time();

            // проверим, что пользователь не удален
            /** @var Rcsc $rcsc */
            $rcsc = Rcsc::find()->where(['id' => $userId])->one();

            if ($rcsc->status_id == Rcsc::STATUS_REJECTED['val']) {
                $this->stdout(
                    "Нельзя добавить базу удаленному пользователю!\n",
                    Console::FG_RED,
                    Console::BOLD
                );
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $dataBases = Database::find()->where(['id' => $baseId])->one();

            if (empty($dataBases)) {
                $this->stdout(
                    "Нельзя добавить  пользователю несуществующую базу данных!\n",
                    Console::FG_RED,
                    Console::BOLD
                );
                return ExitCode::UNSPECIFIED_ERROR;
            }

            if ($rcscHasDatabase->save()) {
                $this->stdout(
                    "База данных успешно добавлена!\n",
                    Console::FG_GREEN,
                    Console::BOLD
                );

                return ExitCode::OK;
            }
        }

        $this->stdout(
            "Невозможно добавить базу! Возможно, что она уже добавлена.\n",
            Console::FG_RED,
            Console::BOLD
        );

        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * Удаляет Пользователю РЦЦС ($userId) Базу ($baseId). На вход принимает: int $userId, int $baseId
     *
     * @param int $userId
     * @param int $baseId
     * @return int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUserBaseRemove(int $userId, int $baseId): int
    {
        // Проверим, что такая БД уже есть;
        $rcscHasDatabase = RcscHasDatabase::find()
            ->where(['rcsc_id' => $userId])
            ->andWhere(['database_id' => $baseId])
            ->one();

        if (!empty($rcscHasDatabase)) {

            if ($rcscHasDatabase->delete()) {
                $this->stdout(
                    "База данных успешно удалена!\n",
                    Console::FG_GREEN,
                    Console::BOLD
                );
                return ExitCode::OK;
            }
        }

        $this->stdout(
            "Невозможно удалить базу данных! Возможно, что она уже отсутствует  у выбранного пользователя РЦЦС!\n",
            Console::FG_GREEN,
            Console::BOLD
        );

        return ExitCode::UNSPECIFIED_ERROR;
    }

//    /**
//     * Добавить все базы админу
//     *
//     * @param int $userId
//     */
//    public function actionAddAllDb(int $userId)
//    {
//        /** @var Database[] $dbs */
//        $dbs = Database::find()->all();
//        foreach ($dbs as $db) {
//            $this->actionUserBaseAdd($userId, $db->id);
//        }
//    }
}



