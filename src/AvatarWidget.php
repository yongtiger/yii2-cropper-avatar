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
use yii\bootstrap\Widget;
use yongtiger\cropperavatar\AvatarAsset;
use yongtiger\cropperavatar\models\UploadForm;

/**
 * Class AvatarWidget
 *
 * @package yongtiger\cropperavatar
 */
class AvatarWidget extends Widget
{
    /**
     * @var string
     */
	public $noImageUrl;

    ///[isModal]
    /**
     * @var bool Whether show avatar cropper by modal mode
     */
    public $isModal = false;

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
        parent::init();

        $this->registerTranslations();

        $bundle = $this->registerClientScript();
        $this->noImageUrl = $this->noImageUrl ? : $bundle->baseUrl . '/images/avatar.png';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('index', ['model' => new UploadForm()]);
    }

    /**
     * Registers necessary JavaScript.
     *
     * @return yii\web\AssetBundle the registered asset bundle instance
     */
    public function registerClientScript()
    {
        return AvatarAsset::register($this->view);
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