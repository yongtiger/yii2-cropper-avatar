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
use yii\base\InvalidConfigException;
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
     * @var callable PHP callback, which should be triggered in case of successful avatar upload.
     * 
     * For example:
     *
     * ```php
     * public function onSuccessCallback($avatarUrl)
     * {
     *     // saving avatar url comes here
     * }
     * ```
     *
     */
    public $successCallback;

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

                $result = $model->upload();

                ///[Yii2 cropper avatar:successCallback]
                if ($this->successCallback) {
                    if (!is_callable($this->successCallback)) {
                        throw new InvalidConfigException('"' . get_class($this) . '::successCallback" should be a valid callback.');
                    }
                    call_user_func($this->successCallback, $result['result']);
                }

                return $result;
            }
        }
        return $this->controller->goHome();
    }
}