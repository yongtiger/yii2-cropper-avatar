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
     * @var JSONstring
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

                // Avatar upload path
                'dstImageFilepath' => Yii::$app->user->isGuest ? '/uploads/avatar/0' : '/uploads/avatar/' . Yii::$app->user->identity->id,

                // Avatar upload file name
                'dstImageFilename' => date('YmdHis'),

                // The file name suffix of the original image, empty means no generating
                'original' => 'original',
                
            ], $this->config);

            $dir = Yii::getAlias('@webroot') . $this->config['dstImageFilepath'];
            if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
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
            // [['avatarData', 'imageFile'], 'required'],///?????'avatarSrc'
            [['imageFile'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    /**
     * Uploads a crop avatar.
     *
     * @return JSONstring
     */
    public function upload()
    {
        $crop = new CropAvatar($this->avatarSrc, $this->avatarData, $this->imageFile, $this->config);

        $response = [
          'state'  => 200,
          'message' => $crop-> getMsg(),
          'result' => $crop-> getResult()
        ];

        return json_encode($response);
    }
}