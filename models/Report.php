<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "reports".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $ip
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date'], 'required'],
            [['user_id'], 'integer'],
            [['date'], 'safe'],
            [['ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'ip' => 'Ip',
        ];
    }

	public static function prepareReportAll($dateBegin, $dateEnd, $detailLevel, $userId = null){

		if($detailLevel == 'weeks'){
			$oldBegin = $dateBegin;
			$oldEnd = $dateEnd;

			$dateBegin = mktime(0,0,0,(int)date('n', $dateBegin), (int)date('j', $dateBegin) - (int)date('N', $dateBegin) + 1,(int)date('Y', $dateBegin));
			$dateEnd = mktime(0,0,0,(int)date('n', $dateEnd), (int)date('j', $dateEnd) + (7 - (int)date('N', $dateEnd)),(int)date('Y', $dateEnd));
		}

		if($detailLevel == 'months'){

			$oldBegin = $dateBegin;
			$oldEnd = $dateEnd;

			$dateBegin = mktime(0,0,0,(int)date('n', $dateBegin), 1,(int)date('Y', $dateBegin));
			$dateEnd = mktime(0,0,0,(int)date('n', $dateEnd) + 1, 0,(int)date('Y', $dateEnd));
		}

		$indexes = [
			'dateBegin' => [
				'dayIndex' => (int)date('z', $dateBegin),
				'weekIndex' => (int)date('W', $dateBegin),
				'monthIndex' => (int)date('n', $dateBegin),
				'year' => (int)date('Y', $dateBegin)
			],
			'dateEnd' => [
				'dayIndex' => (int)date('z', $dateEnd),
				'weekIndex' => (int)date('W', $dateEnd),
				'monthIndex' => (int)date('n', $dateEnd),
				'year' => (int)date('Y', $dateEnd)
			]
		];

		$query = Report::find();

		$query->select(['id', 'user_id', 'CAST(`date` AS DATE) as date', 'day_index', 'week_index', 'month_index', 'year']);

		$operator = ($indexes['dateBegin']['year'] === $indexes['dateEnd']['year']) ? 'and' : 'or' ;

		$query->where([
			$operator,
			['and', 'year='.$indexes['dateBegin']['year'], 'day_index>='.$indexes['dateBegin']['dayIndex'], (!empty($userId)) ? 'user_id='.$userId : '' ],
			['and', 'year='.$indexes['dateEnd']['year'], 'day_index<='.$indexes['dateEnd']['dayIndex'], (!empty($userId)) ? 'user_id='.$userId : '' ]
		]);

		$yearsDiff = (int)$indexes['dateEnd']['year'] - (int)$indexes['dateBegin']['year'];

		if(($yearsDiff) >= 2){

			$arr= [];

			for($i = 1; $i < $yearsDiff ; $i++){
				$arr[] = $indexes['dateBegin']['year'] + $i;
			}

			$query->orWhere([
				'and',
				['in', 'year', array_values($arr)],
				(!empty($userId)) ? 'user_id='.$userId : ''
			]);

		}

		$reports = $query->indexBy('id')->asArray()->orderBy('year ASC, day_index ASC')->all();

		if($detailLevel == 'days'){
			$resultArrTiers = ['year','day_index'];
			$dateFormat = 'd.m.y';
			$elements = (($dateEnd - $dateBegin) / 86400);
		}elseif($detailLevel == 'weeks'){
			$resultArrTiers = ['year', 'week_index', 'day_index'];
			$dateFormat = 'd.m.y';
//			$elements = floor((($dateEnd - $dateBegin)) / (86400 * 7));

			$checkBeginYear = (date('W', $dateBegin) == 52 && date('n', $dateBegin) == 1) ? date('Y',$dateBegin) - 1 : date('Y',$dateBegin) ;
			$checkEndYear = (date('W', $dateEnd) == 52 && date('n', $dateEnd) == 1) ? date('Y',$dateEnd) - 1 : date('Y',$dateEnd) ;

			$dateBeginWeekIndexFrom1970 = (($checkBeginYear - 1970) * 52) + (int)date('W', $dateBegin);
			$dateEndWeekIndexFrom1970 = (($checkEndYear - 1970) * 52) + (int)date('W', $dateEnd);

			$elements = $dateEndWeekIndexFrom1970 - $dateBeginWeekIndexFrom1970;

		}elseif($detailLevel == 'months'){
			$resultArrTiers = ['year', 'month_index'];
			$dateFormat = '%b %Y';

			$dateBeginMonthIndexFrom1970 = (((int)date('Y',$dateBegin) - 1970) * 12) + (int)date('n', $dateBegin);
			$dateEndMonthIndexFrom1970 = (((int)date('Y', $dateEnd) - 1970) * 12) + (int)date('n', $dateEnd);

			$elements = $dateEndMonthIndexFrom1970 - $dateBeginMonthIndexFrom1970;
		}

		$reports = ArrayHelper::index($reports, null, $resultArrTiers);

		$repFinalArr = [];
		$holidayArr = [];
		$monthValuesArr = [];

		$daysNamesArr = [
			1 => '\'Понедельник\'',
			2 => '\'Вторник\'',
			3 => '\'Среда\'',
			4 => '\'Четверг\'',
			5 => '\'Пятница\'',
			6 => '\'Суббота\'',
			7 => '\'Воскресенье\''
		];

		$nextWeekDay = null;

		for($i = 0;$i <= $elements;$i++){

			if($detailLevel == 'days'){
				$startingYear = date('Y',$dateBegin);

				$dayTime = mktime(0,0,0,1,($indexes['dateBegin']['dayIndex'] + 1) + $i, $startingYear);

				$day = date($dateFormat, $dayTime);

				$holidayArr[$day] = (int)date('N', $dayTime);
			}elseif($detailLevel == 'weeks'){
				$dayUsed = (empty($nextWeekDay)) ? $dateBegin : $nextWeekDay ;

				$startingYear = date('Y',$dayUsed);
				$weekBorderDays = Report::determineWeekBorderDaysTime(date('z', $dayUsed), $startingYear);

				$day = date($dateFormat, $weekBorderDays['firstDayOfWeekTime']).' - '.date($dateFormat, $weekBorderDays['lastDayOfWeekTime']);

				$lastDay = date('d.m.Y', $weekBorderDays['lastDayOfWeekTime']);

				$lastArr = explode('.',$lastDay);

				$nextWeekDay = mktime(0,0,0,$lastArr[1],$lastArr[0] + 1,$lastArr[2]);

			}elseif($detailLevel == 'months'){
				$startingYear = date('Y',$dateBegin);
				$time = mktime(0,0,0,$indexes['dateBegin']['monthIndex'] + $i,1, $startingYear);
				$day = strftime($dateFormat, $time);
				$monthValuesArr[] = strftime('%m', $time);
			}

			$repFinalArr[$day] = 0;
			if($detailLevel !== 'days') $holidayArr[$day] = 0;
		}

		foreach($reports as $yearKey => $yearVal){
			foreach($yearVal as $subArrKey => $subArrVal){
					if($detailLevel == 'days'){

						$currentDayTime = mktime(0,0,0,1,$subArrKey + 1,$yearKey);

						$label = date($dateFormat, $currentDayTime);

						$countItems = count($subArrVal);

					}elseif($detailLevel == 'weeks'){

						$weekBorderDays = Report::determineWeekBorderDaysTime(array_keys($subArrVal)[0], $yearKey);
						$label = date($dateFormat, $weekBorderDays['firstDayOfWeekTime']).' - '.date($dateFormat, $weekBorderDays['lastDayOfWeekTime']);

						$countItems = Report::countSubArrays($subArrVal);

					}elseif($detailLevel == 'months'){

						$time = mktime(0,0,0,$subArrKey,1,$yearKey);

						$label = strftime($dateFormat, $time);
						$countItems = count($subArrVal);

					}
					$repFinalArr[$label] = $countItems;
			}
		}

		$keys = array_keys($repFinalArr);

		$distinctIps = Report::getDistinctIps($indexes, $keys, $detailLevel,$userId);
		$filteredIps = Report::matchDistinctIpsWithResults($keys, $distinctIps, $detailLevel, $monthValuesArr);

		return [
			'days' => implode(',', $keys),
			'clicks' => implode(',', array_values($repFinalArr)),
			'holidays' => implode(',', $holidayArr),
			'dateStart' => date('d.m.Y',(empty($oldBegin)) ? $dateBegin : $oldBegin),
			'dateEnd' => date('d.m.Y',(empty($oldEnd)) ? $dateEnd : $oldEnd),
			'numberOfDays' => (int)(($dateEnd - $dateBegin) / 86400),
			'daysNames' => implode(',', $daysNamesArr),
			'ips' => implode(',', $filteredIps)
		];
	}

	public static function countSubArrays($arr){
		$val = 0;

		foreach($arr as $item){
			$val += count($item);
		}

		return $val;
	}

	public static function determineWeekBorderDaysTime($day, $year, $dateFormat = 'd.m'){

		$dayInYearIndex = $day + 1;

		$dayOfWeekTime = mktime(0,0,0,1,$dayInYearIndex,$year);
		$dayOfWeekIndex = date('N', $dayOfWeekTime);

		switch($dayOfWeekIndex){
			case 1:
				$offsetToFirstDayOfWeek = 0;
				$offsetToLastDayOfWeek = 6;
				break;
			case 2:
				$offsetToFirstDayOfWeek = 1;
				$offsetToLastDayOfWeek = 5;
				break;
			case 3:
				$offsetToFirstDayOfWeek = 2;
				$offsetToLastDayOfWeek = 4;
				break;
			case 4:
				$offsetToFirstDayOfWeek = 3;
				$offsetToLastDayOfWeek = 3;
				break;
			case 5:
				$offsetToFirstDayOfWeek = 4;
				$offsetToLastDayOfWeek = 2;
				break;
			case 6:
				$offsetToFirstDayOfWeek = 5;
				$offsetToLastDayOfWeek = 1;
				break;
			case 7:
				$offsetToFirstDayOfWeek = 6;
				$offsetToLastDayOfWeek = 0;
				break;
		}

            // изначально было mktime(0,0,0,1,$dayInYearIndex -/+ $offsetToFirstDayOfWeek,$year, 0);
			$firstDayOfWeekTime = mktime(0,0,0,1,$dayInYearIndex - $offsetToFirstDayOfWeek,$year);
			$lastDayOfWeekTime = mktime(0,0,0,1,$dayInYearIndex + $offsetToLastDayOfWeek,$year);

		return [
			'firstDayOfWeekTime' => $firstDayOfWeekTime,
			'lastDayOfWeekTime' => $lastDayOfWeekTime,
		];

	}

	public static function determineRangeDates($range){

		switch($range){

			case '7days':
				$dateStart = mktime(0,0,0, date('n'), date('j') - 6, date('Y'));
				$dateEnd = mktime(0,0,0, date('n'), date('j'), date('Y'));
				break;
			case '30days':
				$dateStart = mktime(0,0,0, date('n'), date('j') - 29, date('Y'));
				$dateEnd = mktime(0,0,0, date('n'), date('j'), date('Y'));
				break;
			case '90days':
				$dateStart = mktime(0,0,0, date('n'), date('j') - 89, date('Y'));
				$dateEnd = mktime(0,0,0, date('n'), date('j'), date('Y'));
				break;
			case '365days':
				$dateStart = mktime(0,0,0, date('n'), date('j') - 364 , date('Y'));
				$dateEnd = mktime(0,0,0, date('n'), date('j'), date('Y'));
				break;
		}

		return [
			'dateStart' => $dateStart,
			'dateEnd' => $dateEnd
		];
	}

	public static function countFilterGroup($userId, $numberOfDays, $currentDayTime){

		--$numberOfDays;

		$currentDay =  date('z.Y', $currentDayTime);
		$currentDayArr = explode('.', $currentDay);

		$dateBegin =  date('z.Y', mktime(0,0,0,1,($currentDayArr[0] + 1) - $numberOfDays, $currentDayArr[1]));
		$dateBeginArr = explode('.', $dateBegin);

		$operator = ($currentDayArr[1] === $dateBeginArr[1]) ? 'and' : 'or' ;

		return Report::find()->where([
			$operator,
			['and', 'year='.$dateBeginArr[1], 'day_index>='.$dateBeginArr[0], 'user_id='.$userId],
			['and', 'year='.$currentDayArr[1], 'day_index<='.$currentDayArr[0], 'user_id='. $userId ]
		])->count();

	}

	public static function getDistinctIps($indexes, $keys, $detailLevel, $userId){

		switch($detailLevel){
			case 'days' : 	$groupColumn = 'day_index';break;
			case 'weeks' :$groupColumn = 'week_index';break;
			case 'months' :$groupColumn = 'month_index';break;
			default: 	$groupColumn = 'day_index';break;
		}

		$query = Report::find();

		$query->select('COUNT(DISTINCT ip) as ips, day_index, week_index, month_index, year,id');

		$operator = ($indexes['dateBegin']['year'] === $indexes['dateEnd']['year']) ? 'and' : 'or' ;

		$query->where([
			$operator,
			['and', 'year='.$indexes['dateBegin']['year'], $groupColumn.'>='.$indexes['dateBegin'][Report::underscoreToCamelCase($groupColumn)], (!empty($userId)) ? 'user_id='.$userId : '' ],
			['and', 'year='.$indexes['dateEnd']['year'], $groupColumn.'<='.$indexes['dateEnd'][Report::underscoreToCamelCase($groupColumn)], (!empty($userId)) ? 'user_id='.$userId : '' ]
		]);

		$yearsDiff = (int)$indexes['dateEnd']['year'] - (int)$indexes['dateBegin']['year'];

		if(($yearsDiff) >= 2){

			$arr= [];

			for($i = 1; $i < $yearsDiff ; $i++){
				$arr[] = $indexes['dateBegin']['year'] + $i;
			}

			$query->orWhere([
				'and',
				['in', 'year', array_values($arr)],
				(!empty($userId)) ? 'user_id='.$userId : ''
			]);
		}

		$arr = $query->asArray()->groupBy([$groupColumn, 'year'])->orderBy([
			'year' => SORT_ASC,
			$groupColumn => SORT_ASC
		])->all();

		$arr = ArrayHelper::index($arr, null, ['year', $groupColumn]);

		return $arr;
	}

	public static function underscoreToCamelCase($str){
		return str_replace('_', '', lcfirst(ucwords($str, '_')));
	}

	public static function matchDistinctIpsWithResults($keys, $distinctIps, $detailLevel, $monthValuesArr){

		$arr = [];

		if($detailLevel == 'weeks'){

			foreach($keys as $key){
				$startDate = explode(' - ', $key);

				$weekStartDate = explode('.', $startDate[0]);
				$weekStartDate[2] = '20'.$weekStartDate[2];

				$startDate = implode('.', $weekStartDate);

				$formatDate = date('W.Y', strtotime($startDate));
				$formatDate = explode('.',$formatDate);

				$week = (int)$formatDate[0];
				$year = $formatDate[1];

				$arr[] = (!empty($distinctIps[$year][$week][0]['ips'])) ? $distinctIps[$year][$week][0]['ips'] : 0 ;
			}

		}elseif($detailLevel == 'months'){
			$i = 0;
			foreach($keys as $key){
				$monthKey = explode(' ', $key);
				$year = $monthKey[1];

				$formatDate = date('n.Y', strtotime('01.'.$monthValuesArr[$i].'.'.$year));
				$formatDate = explode('.',$formatDate);

				$month = (int)$formatDate[0];
				$year = $formatDate[1];

				$arr[] = (!empty($distinctIps[$year][$month][0]['ips'])) ? $distinctIps[$year][$month][0]['ips'] : 0 ;

				$i++;
			}

		}else{

			foreach($keys as $key){
				$date = explode('.', $key);
				$date[2] = '20'.$date[2];
				$date = implode('.', $date);

				$formatDate = date('z.Y', strtotime($date));
				$formatDate = explode('.',$formatDate);

				$day = (int)$formatDate[0];
				$year = $formatDate[1];

				$arr[] = (!empty($distinctIps[$year][$day][0]['ips'])) ? $distinctIps[$year][$day][0]['ips'] : 0 ;
			}
		}

		return $arr;
	}

}
