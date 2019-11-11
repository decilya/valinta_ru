<div class="otherArticleBlock">

	<strong>А эти статьи вы прочитали?</strong><br/>

	<?php foreach($otherArticles as $item) : ?>

		<a href="/<?= $item->alias ?>"><?= $item->title ?></a><br/>

	<?php endforeach; ?>
</div>