<?php ///[Yii2 cropper avatar]

/**
 * Yii2 cropper avatar
 *
 * @link        http://www.brainbook.cc
 * @see         https://github.com/yongtiger/yii2-cropper-avatar
 * @author      Tiger Yong <tigeryang.brainbook@outlook.com>
 * @copyright   Copyright (c) 2016 BrainBook.CC
 * @license     http://opensource.org/licenses/MIT
 */

namespace yongtiger\cropperavatar;

use Yii;
use yii\bootstrap\InputWidget;
use yii\helpers\Html;
use yongtiger\cropperavatar\AvatarAsset;
use yongtiger\cropperavatar\models\UploadForm;

/**
 * Class AvatarWidget
 *
 * @package yongtiger\cropperavatar
 */
class AvatarWidget extends InputWidget
{
    ///[InputWidget]
    /**
     * @inheritdoc
     */
    public $name;

    /**
     * @var string
     */
    public $noImageUrl;

    ///[isModal]
    /**
     * @var bool Whether show avatar cropper by modal mode
     */
    public $isModal = true;

    /**
     * @var bool
     */
    public $enableRotateButtons = true;
    /**
     * @var bool
     */
    public $enablePreviewLargelImage = true;

    /**
     * @var bool
     */
    public $enablePreviewMiddlelImage = true;

    /**
     * @var bool
     */
    public $enablePreviewSmalllImage = true;

    /**
     * @inheritdoc
     */
    public function init()
    {

        ///[InputWidget]
        if (empty($this->id)) {
            $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        if (empty($this->name)) {
            $this->name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->getId();
        }
        if (empty($this->value) && $this->hasModel()) {
            $attributeName = $this->attribute;
            $this->value = $this->model->$attributeName;
        }

        parent::init();

        $this->registerTranslations();

        $bundle = $this->registerClientScript();
        $this->noImageUrl = $this->noImageUrl ? : $bundle->baseUrl . '/images/no-avatar.png';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        ///[InputWidget]
        if ($this->hasModel()) {
            $avatarInputId = Html::getInputId($this->model, $this->attribute);
            return $this->render('index', ['model' => $this->model, 'avatarInputId' => $avatarInputId, 'isInputWidget' => true]);
        } else {
            return $this->render('index', ['model' => new UploadForm(), 'isInputWidget' => false]);
        }
        
    }

    /**
     * Registers necessary JavaScript.
     *
     * @return yii\web\AssetBundle the registered asset bundle instance
     */
    public function registerClientScript()
    {
        ///[InputWidget]
        return $this->hasModel() ? AvatarInputWidgetAsset::register($this->view) : AvatarAsset::register($this->view);
    }

    /**
     * Registers the translation files.
     */
    public function registerTranslations()
    {
        ///[i18n]
        ///if no setup the component i18n, use setup in this module.
        if (!isset(Yii::$app->i18n->translations['extensions/yongtiger/yii2-cropper-avatar/*']) && !isset(Yii::$app->i18n->translations['extensions/yongtiger/yii2-cropper-avatar'])) {
            Yii::$app->i18n->translations['extensions/yongtiger/yii2-cropper-avatar/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@vendor/yongtiger/yii2-cropper-avatar/src/messages',    ///default base path
                'fileMap' => [
                    'extensions/yongtiger/yii2-cropper-avatar/message' => 'message.php',  ///category in Module::t() is message
                ],
            ];
        }
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t().
     *
     * @see http://www.yiiframework.com/doc-2.0/yii-baseyii.html#t()-detail
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('extensions/yongtiger/yii2-cropper-avatar/' . $category, $message, $params, $language);
    }
}