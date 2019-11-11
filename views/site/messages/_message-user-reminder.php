<?php
use Yii;
use yii\helpers\Url;

?>

<style>
	.mainBlock {
		width: 100%;
	}

	.mainBlock .header {
		height: 60px;
		width: 100%;
	}

	.mainBlock .header img {
		display: block;
		float: left;
		margin-top: 15px;
		margin-left: 30px;
	}

	.mainBlock .header .contactsBlock {
		display: block;
		float: right;
		margin-right: 30px;
		margin-top: 15px;
	}

	.mainBlock .header .contactsBlock p {
		padding: 0;
		margin: 0;
	}

	.mainBlock .header .contactsBlock p:first-child {
		padding-bottom: 5px;
	}

	.mainBlock h2 {
		text-align: center;
		background-color: #1083ab;
		color: #ffffff;
		padding: 10px 0;
	}

	.mainBlock ul {
		list-style-type: none;
	}

	.mainBlock .text {
		padding: 20px 0 30px 30px;
	}

	.mainBlock .parameters {
		color: #ffffff;
		font-weight: bold;
		font-size: 150%;
		display: inline-block;
		border-top-right-radius: 10px;
		border-bottom-right-radius: 10px;
		padding: 10px 30px 10px 30px;
		background-color: #1083ab;
	}

	.mainBlock .regards {
		font-style: italic;
		text-align: right;
		padding-right: 30px;
	}
</style>
<div class="mainBlock">
	<div class="header">
		<img
			src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAAApCAYAAAGKKhF7AAAKN2lDQ1BzUkdCIElFQzYxOTY2LTIuMQAAeJydlndUU9kWh8+9N71QkhCKlNBraFICSA29SJEuKjEJEErAkAAiNkRUcERRkaYIMijggKNDkbEiioUBUbHrBBlE1HFwFBuWSWStGd+8ee/Nm98f935rn73P3Wfvfda6AJD8gwXCTFgJgAyhWBTh58WIjYtnYAcBDPAAA2wA4HCzs0IW+EYCmQJ82IxsmRP4F726DiD5+yrTP4zBAP+flLlZIjEAUJiM5/L42VwZF8k4PVecJbdPyZi2NE3OMErOIlmCMlaTc/IsW3z2mWUPOfMyhDwZy3PO4mXw5Nwn4405Er6MkWAZF+cI+LkyviZjg3RJhkDGb+SxGXxONgAoktwu5nNTZGwtY5IoMoIt43kA4EjJX/DSL1jMzxPLD8XOzFouEiSniBkmXFOGjZMTi+HPz03ni8XMMA43jSPiMdiZGVkc4XIAZs/8WRR5bRmyIjvYODk4MG0tbb4o1H9d/JuS93aWXoR/7hlEH/jD9ld+mQ0AsKZltdn6h21pFQBd6wFQu/2HzWAvAIqyvnUOfXEeunxeUsTiLGcrq9zcXEsBn2spL+jv+p8Of0NffM9Svt3v5WF485M4knQxQ143bmZ6pkTEyM7icPkM5p+H+B8H/nUeFhH8JL6IL5RFRMumTCBMlrVbyBOIBZlChkD4n5r4D8P+pNm5lona+BHQllgCpSEaQH4eACgqESAJe2Qr0O99C8ZHA/nNi9GZmJ37z4L+fVe4TP7IFiR/jmNHRDK4ElHO7Jr8WgI0IABFQAPqQBvoAxPABLbAEbgAD+ADAkEoiARxYDHgghSQAUQgFxSAtaAYlIKtYCeoBnWgETSDNnAYdIFj4DQ4By6By2AE3AFSMA6egCnwCsxAEISFyBAVUod0IEPIHLKFWJAb5AMFQxFQHJQIJUNCSAIVQOugUqgcqobqoWboW+godBq6AA1Dt6BRaBL6FXoHIzAJpsFasBFsBbNgTzgIjoQXwcnwMjgfLoK3wJVwA3wQ7oRPw5fgEVgKP4GnEYAQETqiizARFsJGQpF4JAkRIauQEqQCaUDakB6kH7mKSJGnyFsUBkVFMVBMlAvKHxWF4qKWoVahNqOqUQdQnag+1FXUKGoK9RFNRmuizdHO6AB0LDoZnYsuRlegm9Ad6LPoEfQ4+hUGg6FjjDGOGH9MHCYVswKzGbMb0445hRnGjGGmsVisOtYc64oNxXKwYmwxtgp7EHsSewU7jn2DI+J0cLY4X1w8TogrxFXgWnAncFdwE7gZvBLeEO+MD8Xz8MvxZfhGfA9+CD+OnyEoE4wJroRIQiphLaGS0EY4S7hLeEEkEvWITsRwooC4hlhJPEQ8TxwlviVRSGYkNimBJCFtIe0nnSLdIr0gk8lGZA9yPFlM3kJuJp8h3ye/UaAqWCoEKPAUVivUKHQqXFF4pohXNFT0VFysmK9YoXhEcUjxqRJeyUiJrcRRWqVUo3RU6YbStDJV2UY5VDlDebNyi/IF5UcULMWI4kPhUYoo+yhnKGNUhKpPZVO51HXURupZ6jgNQzOmBdBSaaW0b2iDtCkVioqdSrRKnkqNynEVKR2hG9ED6On0Mvph+nX6O1UtVU9Vvuom1TbVK6qv1eaoeajx1UrU2tVG1N6pM9R91NPUt6l3qd/TQGmYaYRr5Grs0Tir8XQObY7LHO6ckjmH59zWhDXNNCM0V2ju0xzQnNbS1vLTytKq0jqj9VSbru2hnaq9Q/uE9qQOVcdNR6CzQ+ekzmOGCsOTkc6oZPQxpnQ1df11Jbr1uoO6M3rGelF6hXrtevf0Cfos/ST9Hfq9+lMGOgYhBgUGrQa3DfGGLMMUw12G/YavjYyNYow2GHUZPTJWMw4wzjduNb5rQjZxN1lm0mByzRRjyjJNM91tetkMNrM3SzGrMRsyh80dzAXmu82HLdAWThZCiwaLG0wS05OZw2xljlrSLYMtCy27LJ9ZGVjFW22z6rf6aG1vnW7daH3HhmITaFNo02Pzq62ZLde2xvbaXPJc37mr53bPfW5nbse322N3055qH2K/wb7X/oODo4PIoc1h0tHAMdGx1vEGi8YKY21mnXdCO3k5rXY65vTW2cFZ7HzY+RcXpkuaS4vLo3nG8/jzGueNueq5clzrXaVuDLdEt71uUnddd457g/sDD30PnkeTx4SnqWeq50HPZ17WXiKvDq/XbGf2SvYpb8Tbz7vEe9CH4hPlU+1z31fPN9m31XfKz95vhd8pf7R/kP82/xsBWgHcgOaAqUDHwJWBfUGkoAVB1UEPgs2CRcE9IXBIYMj2kLvzDecL53eFgtCA0O2h98KMw5aFfR+OCQ8Lrwl/GGETURDRv4C6YMmClgWvIr0iyyLvRJlESaJ6oxWjE6Kbo1/HeMeUx0hjrWJXxl6K04gTxHXHY+Oj45vipxf6LNy5cDzBPqE44foi40V5iy4s1licvvj4EsUlnCVHEtGJMYktie85oZwGzvTSgKW1S6e4bO4u7hOeB28Hb5Lvyi/nTyS5JpUnPUp2Td6ePJninlKR8lTAFlQLnqf6p9alvk4LTduf9ik9Jr09A5eRmHFUSBGmCfsytTPzMoezzLOKs6TLnJftXDYlChI1ZUPZi7K7xTTZz9SAxESyXjKa45ZTk/MmNzr3SJ5ynjBvYLnZ8k3LJ/J9879egVrBXdFboFuwtmB0pefK+lXQqqWrelfrry5aPb7Gb82BtYS1aWt/KLQuLC98uS5mXU+RVtGaorH1futbixWKRcU3NrhsqNuI2ijYOLhp7qaqTR9LeCUXS61LK0rfb+ZuvviVzVeVX33akrRlsMyhbM9WzFbh1uvb3LcdKFcuzy8f2x6yvXMHY0fJjpc7l+y8UGFXUbeLsEuyS1oZXNldZVC1tep9dUr1SI1XTXutZu2m2te7ebuv7PHY01anVVda926vYO/Ner/6zgajhop9mH05+x42Rjf2f836urlJo6m06cN+4X7pgYgDfc2Ozc0tmi1lrXCrpHXyYMLBy994f9Pdxmyrb6e3lx4ChySHHn+b+O31w0GHe4+wjrR9Z/hdbQe1o6QT6lzeOdWV0iXtjusePhp4tLfHpafje8vv9x/TPVZzXOV42QnCiaITn07mn5w+lXXq6enk02O9S3rvnIk9c60vvG/wbNDZ8+d8z53p9+w/ed71/LELzheOXmRd7LrkcKlzwH6g4wf7HzoGHQY7hxyHui87Xe4Znjd84or7ldNXva+euxZw7dLI/JHh61HXb95IuCG9ybv56Fb6ree3c27P3FlzF3235J7SvYr7mvcbfjT9sV3qID0+6j068GDBgztj3LEnP2X/9H686CH5YcWEzkTzI9tHxyZ9Jy8/Xvh4/EnWk5mnxT8r/1z7zOTZd794/DIwFTs1/lz0/NOvm1+ov9j/0u5l73TY9P1XGa9mXpe8UX9z4C3rbf+7mHcTM7nvse8rP5h+6PkY9PHup4xPn34D94Tz+49wZioAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAobSURBVHic7VwHjFVFFJ2FL7gqasDCWhBNLERsKAqxsCoWFFsUe4nG3oiaWDBighFbjAIqMRoxmCAmGo1oLLGgYom9Y7CLCopYUQSV9Zz/7uyfnZ2ZN+/99/9+dE9yd/6bd+fOfVPu3Lnz3pba2tqURlNTEy7bmlQGlPpNfuYcpLcYQuZByBbRAlj4x7F7t2f0nfT0laZGTE2tbC1LmkEzQ9jwprFqhv0o+r5xPRnJeSVLo+1Bb6epbQorLT5/rya0QxtUV1L4XOTdaqvua9xSEb2gn53J31kKtz9CufaxioLa20RrY2vl7AWzu+zWNu63gL6w8tprbAYtRebVPlUhfCHu95bC7Xxsg5fQA8Plepx+JKOgrycuLwswe8FQ70Akr4JuBO0Fno18mlWLci/aWgPs0lnUBfSVQ8FOUyS3AobQFUhOBt1t8QwwO9Y3xVwDwM5z8ZSMmz0cI+h90GAQu+TR2KfSFTkqf08u2+VRgVfQDQ+jGw5OaVLnYIr5beRtY8vzDUIOPtqTEbg/LaBU1bCtocaFQkRtFdC2TAM2bR0kB6mkBTZGa0xHK5xYMwX4x5yGYhOJuaA7YWQ69WXh0xACv0ZyGwRONO4NAm2lksFymqtwjCLCQwPew6sALZ1Mmw62WE9No1VGI885HY0ptwS0Bmg+eAdUbldWF6PYaN8gNLEItK78fkQZ09EFVNJHKtnYytflOsgzDZGtXe+0ZS4rXPK8LQCm5WDqULCIyu3fHRSwB5NvcHmsXLRFNEEFetu2wMCvmKJrhQRUC6cp9kGa7huVjHIqNh40oQh70FUoDwH0wJtIdgjwHQkLfR9SNgAN5EjQ7qAXVGVGrZTgUvQr0j6gFgy3hS4m9Px8lZifcSAaa5oY5nHJWqRSTJPI6OQrVad6MeAI4MNzOVzo2YzNQKJdYr1SzZeUXmtfj1ebiiIbBbKORzI+y86UsM0wh/MiY9U6FdfHBMqPAt9PKmIEuFDEKKh2ibCXgR8gkMN6VoTgG8D/uKEIfY2hoFcsmWl+SifeyIeaCP7LI/iC+nVaiCH0ETDfhJ8XBOS9DppQxAIdA0/D0B55GwC8XKl+S5Pt9ERQ4YUQsKdKwiU2/sT9obj/p5XfIpv3ujSKD6h/FySvgX6wbjn1C7liO4B5GX72svKbJV0V97ll107nAlwvqfYBcmC0SpxkjfIQFxuWql/ZEwMtc3ljEvPqAJPPuP8uVoHtQlqmuY9582V74uMf4Mo3kccTZAvTmPRslLW8GsTsx8rAw58rPxlTWQy6SjcIGmK4v2RjI7oBgCmgpaqyjHws6R6FalRn0BXeQCUbHC+wDxgsP3cGMbo2BzQdtBS9/1dtVawtOAKCDw9copJIMXG+pCeBPgUNyxPXbyS0TwGfL48HJM91Knl4nq38rhJHiGAEc44OrfoqadSNEBFjA56XdJGknAYfqEqDrAJaaaeBeTzwMpJhoGvRQ5cZPNrC3yvpB5KWRwN8gYvzVl7UyIAcOjvcCTrjpyGYI4DWfDnoUtBlIth7ZiWgP/Bh1kqLgsPtzt8AtOYQyGWumWs+rnkCPC5UGDyMDuWez41gC2wboJe5KWiEuSllz+afwOZnfpormnaiZtRzm5U3BLxv+eTZ57oh/ex4wPs6Fg08FVIevFPNylCOkaWZoAOEhSc7z+H+iJCcCNgPTzCGGbW3SNPPtQpo6x7C/SKUgdFrQTspa9coKMJLzL3NjtHPFRCZIu9SeAGeMeCh8RuUVams4MPnKRern88PeAh0qOfeAkk7CM8YzqoHovTzRYQOCzyIK0qkRPj6GZUsFKifsYGjob8zFObSL+QJfg9az86E8O89wrui5+mlmnaGBo7nHN5TbRuhBmBPf2vlHWb83hH0hnXftWTVDFxhrLCXiSj92hsAGxqeDOn5TeH87XVUcN+5FAFTHby5Tlkj852+Rqx+bAAyDgF9i0ZwyYrBqmi8ZXkLdyX4xtyO8vIjj73WzFieG6hWyFhevGr1QaagaCzkUILnh2tLFo/X6GbrwxYa0k1Qt3220I06I0tMNApyBEfjqzufG0qeuerO52zhYPgMvC2SdwwGw8yidelGOsxFkFsFhhOOA22tspvDF2EKd0PK82J9Qn+HSjbXswy+XVWyldKdP66787sOJel4rmVDqpBDV6wVM/p2pPtIHoMJ3FK+a/CNUUmMfSe55gBZIj4qLcNA8b66USfQAjynOnY+Z+sZpkscA3QiA6mnyyWPzI4FfakqrigjxzT9R8g1B8jDqmIddu076enVsz6ApUPQoWmE+GOjgQNgmJkROtzxAQ1/FJJr5PIXkcmZ3yx53HvwvYTr5ZoD5CJV+U5nDDqfDuE8kPOV5m7UBlU7geh8zmq9hv+jEmvCmMyGkkefgDP9MbnmANlXJUcpnJEXo/Nnq2QZ6Z6hdYZzAKBTj1aVQ0CCncZt2y8WHzt5jpHVUyXn5Sb2F9Lg29Wfy++p6Pyb5bpZ1RGNsFxAh4FIWkEjJB3YUQX3Bx5FwhcNngnlNlWVd4LZafOoMO7x3JDKs8NowlfJWffjkHU2nFC+arNhKnfBaBB/4PPAvbroF3o/8hoZBPqkmZHxucjbXCUfqfJtmXVy1svz1vLR3I9j997FvCFfJq2QOrgzoO/AwBHf4OE5KC3OU9W+kpTFAli8K4QM1rZe4GGsgx+27aeSDx37yTN8BHpAJZ+D/VyNjq5B63mO6PYL+gBgOl0GwUjJ2gT0DugzlTxkHvCl+qFt4RAkTV8vIUYV+4O2BY3SDNCL/sa+EJM7gJ0TPZTlqAYGE/UfInQ1+Hi6ORg6L66tivHtF+MEckRzFG8u14NU/qNgjsrt0QC/64yUjwh4gOc7Eqa/wS1kVVvHgsAT5LPkN3c8vtNkdgS33VGTJ2aZqrb9UgcAKlgBQTxbY2y/2u/29oS8r0Sxe1QlaJQXq1VZvhDwBN24DJ2mE1tXW1+R7Re1DeQROyqlGeM+vWfOyk6EnDmQcwV+Twjw8fNpBpT4StEfDfSORUOg6PaLjgNAGA9v6As8G1vGAN/bvEd+p33btSUVz1HHfw6c6WiL76zsQtsvUyAIgmdDqVPw864Mxaah3FXG9ScqbAZnoI4z5XfdXi/pIrCjQsvYQuNFTb3eF9p+mSOBUGKaBDDGR7DPBv8pVl6rSk4I+3vKHCL0fwC/yn1SZVtWW1WB7ccBwFfo+uiMfpOf6e/7al4DnXqlbA9PCLBxqzjSzuTnyEhaUJ7vC/AlrcNV8u9RqAu3R/yHQU+A+A9j+N0297Kmp9tpG7ayQrawJTwjg2lsKx6n8982MDDGuAu3cNzKsW16SJlC24+F+CnELQbDgph3w1zfkFrYDPS3T5an/HUYfJPMDAZZUpWp8EZHz2rFm5OfQZnHVOW8JKYMg0oTVSVa6+MLth/fibsVnfSgSr42Dv3TkFqCEaqDoEva93rdKBj/Aqg/hhVl5uyKAAAAAElFTkSuQmCC"/>

		<div class="contactsBlock">
			<p>Телефон: <?= Yii::$app->params['mainSystemPhone'] ?></p>

			<p>E-mail: <a href="mailto:<?= Yii::$app->params['mailFrom']; ?>"><?= Yii::$app->params['mailFrom']; ?></a></p>
		</div>
	</div>
	<h2>Уважаемый(ая) <?= $user->fio ?>!</h2>

	<p>Сообщаем, что Ваша анкета №<strong><?= $user->real_id ?></strong> не обновлялась более <strong><?= $daysInactive ?></strong> календарных дней.</p>

	<p>Внести необходимые изменения и обновить анкету Вы всегда можете в Личном кабинете сметчика (ЛК).
		Обновление анкетных данных повышает рейтинг Вашей анкеты и вероятность получения заказа. Изменение рейтинга анкеты происходит один раз в сутки.</p>



		В случае возникновения каких-либо вопросов, пожалуйста, свяжитесь с нами по
		телефону:<strong><?= Yii::$app->params['mainSystemPhone']; ?></strong><br><br>

		С уважением,<br>
    команда Valinta.ru

    <div style='font-size: 14px; text-align: center;'>
        <p style='font-size: 14px; text-align: center;'>Данное письмо создано и отправлено автоматически, не нужно на него отвечать.</p>
    </div>
</div>