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

namespace yongtiger\cropperavatar\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yongtiger\cropperavatar\CropAvatar;
use yongtiger\cropperavatar\AvatarWidget;

/**
 * Class UploadForm
 *
 * @see http://www.yiiframework.com/doc-2.0/guide-input-file-upload.html#creating-models
 *
 * @package yongtiger\cropperavatar\models
 */
class UploadForm extends Model
{
    /**
     * @var array
     */
    public $config = [];

    /**
     * @var string
     */
    public $avatarSrc;

    /**
     * @var string JSON string
     */
    public $avatarData;

    /**
     * @var yii\web\UploadedFile
     */
    public $imageFile;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app->request->isPost) {
            $this->config = ArrayHelper::merge([

                // Default width of the destination image
                'dstImageWidth' => 200,

                // Default height of the destination image
                'dstImageHeight' => 200,

                // Default width of the middle image, empty means no generating
                'middleImageWidth'=> 100,

                // Default height of the middle image, empty means no generating
                'middleImageHeight'=> 100,

                // Default width of the small image, empty means no generating
                'smallImageWidth' => 50,

                // Default height of the small image, empty means no generating 
                'smallImageHeight' => 50,

                ///[v0.10.2 (ADD# dstImageUri, CHG# dstImageFilepath)]
                // Avatar upload path
                ///Note: Usually disable guset from uploading avatar!
                'dstImageFilepath' => Yii::$app->user->isGuest ? '@webroot/uploads/avatar/0' : '@webroot/uploads/avatar/' . Yii::$app->user->id,

                // Avatar uri
                'dstImageUri' => Yii::$app->user->isGuest ? '@web/uploads/avatar/0' : '@web/uploads/avatar/' . Yii::$app->user->id,
                ///[http://www.brainbook.cc]
                
                // Avatar upload file name
                'dstImageFilename' => date('YmdHis'),

                // The file name suffix of the original image, empty means no generating
                'original' => 'original',
                
            ], $this->config);

            ///[v0.10.2 (ADD# dstImageUri, CHG# dstImageFilepath)]
            $this->config['dstImageUri'] = Yii::getAlias($this->config['dstImageUri']);
            $this->config['dstImageFilepath'] = Yii::getAlias($this->config['dstImageFilepath']);
            if (!is_dir($this->config['dstImageFilepath']) && !mkdir($this->config['dstImageFilepath'], 0777, true)) {
                throw new Exception('Fails to make upload directory!');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['imageFile'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'avatarSrc' => AvatarWidget::t('message', 'Avatar Src'),
            'avatarData' => AvatarWidget::t('message', 'Avatar Data'),
            'imageFile' => AvatarWidget::t('message', 'Image File'),
        ];
    }

    /**
     * Uploads a crop avatar.
     *
     * @return array
     */
    public function upload()
    {
        $crop = new CropAvatar($this->avatarSrc, $this->avatarData, $this->imageFile, $this->config);

        ///[Yii2 cropper avatar:FORMAT_JSON]
        // return json_encode($response);
        return [
          'state'  => 200,
          'message' => $crop->getMsg(),
          'result' => $crop->getResult(),
          'params' => $crop->getParams()  ///[v0.10.5 (ADD# getParams())]
        ];
    }
}