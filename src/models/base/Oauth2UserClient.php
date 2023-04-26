<?php
// This class was automatically generated by a giiant build task.
// You should not change it manually as it will be overwritten on next build.

namespace rhertogh\Yii2Oauth2Server\models\base;

use Yii;

/**
 * This is the base-model class for table "oauth2_user_client".
 *
 * @property integer $user_id
 * @property integer $client_id
 * @property boolean $enabled
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2Client $client
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2Scope[] $scopes
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2UserClientScope[] $userClientScopes
 * @property string $aliasModel
 *
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
abstract class Oauth2UserClient extends \rhertogh\Yii2Oauth2Server\models\base\Oauth2BaseActiveRecord
{




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'client_id', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'client_id', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['user_id', 'client_id', 'created_at', 'updated_at'], 'integer'],
            [['enabled'], 'boolean'],
            [['user_id', 'client_id'], 'unique', 'targetAttribute' => ['user_id', 'client_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('oauth2', 'User ID'),
            'client_id' => Yii::t('oauth2', 'Client ID'),
            'enabled' => Yii::t('oauth2', 'Enabled'),
            'created_at' => Yii::t('oauth2', 'Created At'),
            'updated_at' => Yii::t('oauth2', 'Updated At'),
        ];
    }
    
    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2ClientQueryInterface     */
    public function getClient()
    {
        return $this->hasOne(\rhertogh\Yii2Oauth2Server\models\Oauth2Client::class, ['id' => 'client_id'])->inverseOf('userClients');
    }
    
    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2ScopeQueryInterface     */
    public function getScopes()
    {
        return $this->hasMany(\rhertogh\Yii2Oauth2Server\models\Oauth2Scope::class, ['id' => 'scope_id'])->via('userClientScopes');
    }
    
    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2UserClientScopeQueryInterface     */
    public function getUserClientScopes()
    {
        return $this->hasMany(\rhertogh\Yii2Oauth2Server\models\Oauth2UserClientScope::class, ['user_id' => 'user_id', 'client_id' => 'client_id'])->inverseOf('user');
    }


    
    /**
     * @inheritdoc
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2UserClientQueryInterface the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(\rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2UserClientQueryInterface::class, [get_called_class()]);
    }


}
