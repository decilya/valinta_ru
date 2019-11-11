<?php

namespace app\components\rules;

use Yii;
use yii\web\UrlRuleInterface;
use yii\base\BaseObject;

class ContentRule extends BaseObject implements UrlRuleInterface
{
	public function createUrl($manager, $route, $params)
	{

	}

	public function parseRequest($manager, $request)
	{
		$pathInfo = $request->getPathInfo();

		$path = explode('/', $pathInfo);

		if(count($path) === 1){

			$contentPath = Yii::getAlias('@app').'/views/site/content';

			if(is_file($contentPath.'/'.$path[0].'.php')){

				return ['site/content', ['alias' => $path[0]]];
			}
		}

		return false;
	}
}