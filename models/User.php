<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user".
 *
 * @property string $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $mobile
 * @property string $address
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 *
 * @property DistributorLoading[] $distributorLoadings
 * @property DriverLoading[] $driverLoadings
 * @property DriverLoading[] $driverLoadings0
 * @property Product[] $products
 * @property Product[] $products0
 * @property ProductPrice[] $productPrices
 * @property Shop[] $shops
 * @property Shop[] $shops0
 * @property ShopLoading[] $shopLoadings
 * @property UserDetails $createdBy
 * @property UserDetails[] $userDetails
 * @property UserDetails $updatedBy
 * @property UserDetails[] $userDetails0
 * @property UserRoleMapping[] $userRoleMappings
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name', 'username', 'address'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 1024],
            [['mobile'], 'string', 'max' => 25],
            [['username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'username' => 'Username',
            'password' => 'Password',
            'mobile' => 'Mobile',
            'address' => 'Address',
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
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
    	$user = self::find()->where(['user_id' => $id])->one();
    	return isset($user) ? new static($user) : null;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    	foreach (self::$users as $user) {
    		if ($user['accessToken'] === $token) {
    			return new static($user);
    		}
    	}
    
    	return null;
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
    	$user = self::find()->where(['username' => $username])->one();
    	 
    	//         foreach (self::$users as $user) {
    	//             if (strcasecmp($user['username'], $username) === 0) {
    	//                 return new static($user);
    	//             }
    	//         }
    	 
    	if ($user != null) {
    		return new static($user);
    	}
    	 
    	return null;
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
    	return $this->user_id;
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
    	return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
    	return $this->auth_key === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	return $this->password === md5($password);
    }
    

    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		if ($this->isNewRecord) {
    			$this->auth_key = \Yii::$app->security->generateRandomString();
    			$this->password = md5($this->password);
    		}
    		return true;
    	}
    	return false;
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributorLoadings()
    {
        return $this->hasMany(DistributorLoading::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDriverLoadings()
    {
        return $this->hasMany(DriverLoading::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDriverLoadings0()
    {
        return $this->hasMany(DriverLoading::className(), ['driver_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts0()
    {
        return $this->hasMany(Product::className(), ['updated_by' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrices()
    {
        return $this->hasMany(ProductPrice::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shop::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops0()
    {
        return $this->hasMany(Shop::className(), ['driver_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopLoadings()
    {
        return $this->hasMany(ShopLoading::className(), ['created_by' => 'user_id']);
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
    public function getUserDetails()
    {
        return $this->hasMany(User::className(), ['created_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDetails0()
    {
        return $this->hasMany(User::className(), ['updated_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoleMappings()
    {
        return $this->hasMany(UserRoleMapping::className(), ['user_id' => 'user_id']);
    }
}
