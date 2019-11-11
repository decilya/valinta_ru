<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content".
 *
 * @property integer $id
 * @property string $title
 * @property string $alias
 * @property string $content
 * @property integer $content_type_id
 */
class Content extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'alias', 'content', 'content_type_id'], 'required'],
            [['content'], 'string'],
            [['content_type_id'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'alias' => 'Alias',
            'content' => 'Content',
            'content_type_id' => 'Content Type ID',
        ];
    }

	/**
	 * Relation with ContentType Model.
	 */
	public function getContentType(){
		return $this->hasOne(ContentType::className(), ['id' => 'content_type_id']);
	}
}
