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

namespace yongtiger\cropperavatar\actions;

use Yii;
use yii\base\Action;
use yii\web\UploadedFile;
use yii\web\Response;
use yongtiger\cropperavatar\models\UploadForm;

/**
 * Class CropAvatarAction
 *
 * @package yongtiger\cropperavatar\actions
 */
class CropAvatarAction extends Action
{
    /**
     * @var array
     */
    public $config = [];

    /**
     * @inheritdoc
     *
     * @see http://www.yiiframework.com/doc-2.0/guide-input-file-upload.html#wiring-up
     */
    public function run()
    {
        ///[Yii2 cropper avatar:FORMAT_JSON]
        if (Yii::$app->request->isAjax) {
            // Yii::$app->request->enableCsrfValidation = false;    ///?????close csrf
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new UploadForm(['config' => $this->config]);

            if (Yii::$app->request->isPost) {

                $post = Yii::$app->request->post();
                $model->avatarSrc = $post['UploadForm']['avatarSrc'];
                $model->avatarData = $post['UploadForm']['avatarData'];
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                return $model->upload();
            }
        }
        return $this->controller->goHome();
    }
}