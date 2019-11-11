<div class="searchIdBlock" xmlns="http://www.w3.org/1999/html">
    <label for="searchId">№</label>
    <input id="searchId" name="searchId"
           value="<?= (!empty($filterParams) && !empty($filterParams['id'])) ? $filterParams['id'] : '' ?>"
           type="text"/>
</div>

<div class="searchTextBlock">
    <label for="searchText">Текст</label>
    <input id="searchText" name="searchText"
           value="<?= (!empty($filterParams) && !empty($filterParams['text'])) ? $filterParams['text'] : '' ?>"
           type="text" placeholder="ФИО, E-mail, Телефон"/>
</div>

<?php if (empty($requestIndex)): ?>
    <div class="searchUserStatusBlock">
        <label for="searchUserStatusSelect">Статус</label>
        <select id="searchUserStatusSelect" name="searchUserStatus">
            <option value="0"<?= (empty($filterParams['userStatus']) || $filterParams['userStatus'] == 0) ? ' selected' : '' ?>>
                все
            </option>
            <option value="1"<?= (!empty($filterParams['userStatus']) && $filterParams['userStatus'] == 1) ? ' selected' : '' ?>>
                требует проверки
            </option>
            <option value="2"<?= (!empty($filterParams['userStatus']) && $filterParams['userStatus'] == 2) ? ' selected' : '' ?>>
                подтвержден
            </option>
            <option value="3"<?= (!empty($filterParams['userStatus']) && $filterParams['userStatus'] == 3) ? ' selected' : '' ?>>
                отклонен
            </option>
        </select>
    </div>
<?php endif; ?>

<?php if (!empty($requestIndex)) : ?>
    <div class="searchStatusBlock">
        <label for="searchStatusSelect">Статус</label>
        <select id="searchStatusSelect" name="searchStatus">
            <option value="0"<?= (empty($filterParams['status']) || $filterParams['status'] == 0) ? ' selected' : '' ?>>
                все
            </option>
            <option value="1"<?= (!empty($filterParams['status']) && $filterParams['status'] == 1) ? ' selected' : '' ?>>
                новая
            </option>
            <option value="2"<?= (!empty($filterParams['status']) && $filterParams['status'] == 2) ? ' selected' : '' ?>>
                обработана
            </option>
        </select>
    </div>
<?php endif; ?>

<?php if (isset($sortUser)) { ?>
<div class="sortBlock">
    <label for="sortSelect">Сортировка по</label>
    <select id="sortSelect" name="sortUser">
        <option value="0" <?php if (($sortUser == null) && ($sortUser == 0)) { ?>selected <?php } ?>>Статус + изменение
        </option>
        <option value="1" <?php if ($sortUser == 1) { ?>selected <?php } ?>>Номеру по убыванию</option>
        <option value="2" <?php if ($sortUser == 2) { ?>selected <?php } ?>>Номеру по возрастанию</option>
    </select>
</div>
<?php } ?>