<?php

namespace app\modules\structure\models;

use app\helpers\WordHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_department".
 *
 * @property integer $id
 * @property string $department
 * @property integer $department_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Department[] $child
 * @property Department $parent
 */
class Department extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%department}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getChild()
    {
        return $this->hasMany(Department::className(),['department_id'=>'id']);
    }

    public function getParent()
    {
        return $this->hasOne(Department::className(), ['id'=>'department_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department'], 'required'],
            [['department'], 'trim'],
            [['department'], 'string'],
            [['department_id', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('structure', 'ID'),
            'department' => Yii::t('structure', 'Department'),
            'department_id' => Yii::t('structure', 'Head department'),
            'created_at' => Yii::t('structure', 'Created At'),
            'updated_at' => Yii::t('structure', 'Updated At'),
        ];
    }

    public function getDepartmentGenitive() {
        $wordInQuotes = false;
        $depBegin = Word::getAllWords(Word::ABBR);
        $matches = array_filter($depBegin, function($var){ return preg_match("/^$var/i", $this->department); });
        if(count($matches)==0)
        {
            $words = explode(' ', $this->department);
            $_i=0;
            //@todo �� ����������� ��������� ������ � ���� ��� ����

            $properNames = Word::getAllWords(Word::CAPITAL);

            foreach($words as $word)
            {
                if($wordInQuotes)
                {
                    $_i++;
                    continue;
                }
                if(mb_substr($word,0,1,'UTF-8')=='�') {
                    $wordInQuotes = true;
                    $_i++;
                    continue;
                }
                if(mb_substr($word,-1,1,'UTF-8')=='�'){
                    $wordInQuotes = false;
                    $_i++;
                    continue;
                }
                $matches = array_filter($properNames, function($var) use ($word) { return preg_match("/^$var/i", $word); });
                if(count($matches)==0)
                    $words[$_i]=mb_strtolower($word, "UTF-8");
                $words[$_i]=WordHelper::Genitive($words[$_i]);
                $_i++;
            }
            return implode(' ', $words);
        }
        return $this->department;
    }
}
