<?php
$html = '';

if(in_array($alias, ['how-to-choose-smetchik', 'how-much-does-smeta-cost', 'how-to-work-with-smetchik-freelancer'])){

	$html .= "<div class=\"container\">";

	$articles = $a =  '';

	if($alias === 'how-to-choose-smetchik'){
		$articles .= "<a href=\"/how-to-work-with-smetchik-freelancer\">Как правильно работать со сметчиком-фрилансером?</a><br>";
		$articles .= "<a href=\"/how-much-does-smeta-cost\">Сколько стоит составить смету?</a><br>";
        $articles .= "<a href=\"/five_ways_to_protect\">5 способов сметчику-фрилансеру защитить себя от мошенников, которые не хотят оплачивать заказ</a><br>";

		$a= "<a href=\"/\" class=\"btn art-left\">Найти сметчика</a>";
	}elseif($alias === 'how-much-does-smeta-cost'){
		$articles .= "<a href=\"/how-to-choose-smetchik\">Как правильно выбрать сметчика?</a><br>";
		$articles .= "<a href=\"/how-to-work-with-smetchik-freelancer\">Как правильно работать со сметчиком-фрилансером?</a><br>";
        $articles .= "<a href=\"/five_ways_to_protect\">5 способов сметчику-фрилансеру защитить себя от мошенников, которые не хотят оплачивать заказ</a><br>";

		$a= "<a href=\"/register\" class=\"btn art-left\">Зарегистрироваться</a>";
	}elseif($alias === 'how-to-work-with-smetchik-freelancer'){
		$articles .= "<a href=\"/how-much-does-smeta-cost\">Сколько стоит составить смету?</a><br>";
		$articles .= "<a href=\"/how-to-choose-smetchik\">Как правильно выбрать сметчика?</a><br>";
        $articles .= "<a href=\"/five_ways_to_protect\">5 способов сметчику-фрилансеру защитить себя от мошенников, которые не хотят оплачивать заказ</a><br>";

		$a= "<a href=\"/\" class=\"btn art-left\">Найти сметчика</a>";
	}

	$html .= "<div class=\"otherArticleBlock\">";
	$html .= "<strong>А эти статьи вы прочитали?</strong><br/>";
	$html .= $articles;
	$html .= "</div>";

	$html .= "<div class=\"b-site-content__wrap-btn\">";
	$html .= $a;
	$html .= "<a href=\"/order/create\" class=\"btn art-right\">Разместить заказ</a>";
	$html .= "</div>";

	$html .= "</div>";
}

echo $this->render('/site/content/'.$alias, [
	'html' => $html
]);

