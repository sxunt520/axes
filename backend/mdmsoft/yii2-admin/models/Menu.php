<?php

namespace mdm\admin\models;

use Yii;
use mdm\admin\components\Configs;
use yii\helpers\Html;

/**
 * This is the model class for table "menu".
 *
 * @property int $id Menu id(autoincrement)
 * @property string $name Menu name
 * @property int $parent Menu parent
 * @property string $route Route for this menu
 * @property int $order Menu order
 * @property string $data Extra information for this menu
 * @property Menu $menuParent Menu parent
 * @property Menu[] $menus Menu children
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 *
 * @since 1.0
 */
class Menu extends \yii\db\ActiveRecord
{
    public $parent_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Configs::instance()->menuTable;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDb()
    {
        if (Configs::instance()->db !== null) {
            return Configs::instance()->db;
        } else {
            return parent::getDb();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_name'], 'filterParent'],
            [['parent_name'], 'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.', ],
            [['parent', 'route', 'data', 'order'], 'default'],
            ['icon', 'string'],
            [['order'], 'integer'],
            [['route'], 'in',
                'range' => static::getSavedRoutes(),
                'message' => 'Route "{value}" not found.', ],
        ];
    }

    /**
     * Use to loop detected.
     */
    public function filterParent()
    {
        $value = $this->parent_name;
        $parent = self::findOne(['name' => $value]);
        if ($parent) {
            $id = $this->id;
            $parent_id = $parent->id;
            while ($parent) {
                if ($parent->id == $id) {
                    $this->addError('parent_name', 'Loop detected.');

                    return;
                }
                $parent = $parent->menuParent;
            }
            $this->parent = $parent_id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-admin', 'ID'),
            'name' => Yii::t('rbac-admin', 'Name'),
            'parent' => Yii::t('rbac-admin', 'Parent'),
            'parent_name' => Yii::t('rbac-admin', 'Parent Name'),
            'route' => Yii::t('rbac-admin', 'Route'),
            'icon' => Yii::t('rbac-admin', 'Icon'),
            'order' => Yii::t('rbac-admin', 'Order'),
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    public function attributeHints()
    {
        return [
            'icon' => '（参考' . Html::a('icon list', ['/site/demo', 'view' => 'icons'], ['target' => '_blank']). ',只需要fa-后边的字符,不填默认箭头）'
        ];
    }

    /**
     * Get menu parent.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent']);
    }

    /**
     * Get menu children.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(self::className(), ['parent' => 'id']);
    }

    /**
     * Get saved routes.
     *
     * @return array
     */
    public static function getSavedRoutes()
    {
        $result = [];
        foreach (Yii::$app->getAuthManager()->getPermissions() as $name => $value) {
            if ($name[0] === '/' && substr($name, -1) != '*') {
                $result[] = $name;
            }
        }

        return $result;
    }
}
