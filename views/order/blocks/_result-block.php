<?php

/**
 * @var int $typeOfUser
 * @var array $qp
 * @var \app\models\Customer|\app\models\User $user
 */

if (empty($results)) {
    echo "<div class='container'>
        <p class='noResults'>На портале не опубликовано ни одного заказа.</p>
    </div>";
} else {

    if (!isset($positionOrderInResultArr)) {
        $positionOrderInResultArr = 0;
    }

    foreach ($results as $item) {
        $positionOrderInResultArr++;
        echo $this->render('_item-block', [
            'item' => $item,
            'qp' => $qp,
            'positionOrderInResultArr' => $positionOrderInResultArr,
            'typeOfUser' => $typeOfUser,
            'months' => \app\models\Site::MONTHS,
            'user' => $user
        ]);
    }
}