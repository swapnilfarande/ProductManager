<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "product".
 *
 * @property string $product_id
 * @property string $name
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 *
 * @property DistributorLoading[] $distributorLoadings
 * @property DriverLoading[] $driverLoadings
 * @property User $createdBy
 * @property User $updatedBy
 * @property ProductPrice[] $productPrices
 * @property ShopLoading[] $shopLoadings
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'name' => 'Name',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

	public function behaviors() {
		return [ 
				BlameableBehavior::className (),
				[ 
						'class' => TimestampBehavior::className (),
						'value' => new Expression ( 'NOW()' ) 
				] 
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributorLoadings()
    {
        return $this->hasMany(DistributorLoading::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDriverLoadings()
    {
        return $this->hasMany(DriverLoading::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['updated_by' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrices()
    {
        return $this->hasMany(ProductPrice::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopLoadings()
    {
        return $this->hasMany(ShopLoading::className(), ['product_id' => 'product_id']);
    }
}
