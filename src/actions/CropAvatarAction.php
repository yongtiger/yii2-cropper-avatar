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
     * @var callable PHP callback, which should be triggered in case of successful avatar upload, usually to save the avatar operation.
     *
     * Example of `successCallback`:
     *
     * ```php
     * public function actions()
     * {
     *     return [
     * 
     *         'crop-avatar'=>[
     *             'class' => 'yongtiger\cropperavatar\actions\CropAvatarAction',
     *             'successCallback' => [$this, 'saveAvatar'],
     *             // or
     *             'successCallback' => function ($avatarSrc, $isInputWidget) {
     *                 // save $avatarSrc into user table ...
     *                 return;
     *             },
     *             // ...
     *         ],
     *     ],
     * }
     *
     * CropAvatarAction successCallback.
     *
     * @param string $avatarSrc
     * @param string $isInputWidget If set, means that `isInputWidget`, usually not to save the avatar operation
     *
     * public function saveAvatar($avatarSrc, $isInputWidget)  ///[isInputWidget]tell action's successCallback not to save the avatar operation.
     * {
     *     // save $avatarSrc into user table ...
     *     return;
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
        // if (Yii::$app->request->isAjax) {    ///[fix:main.js:this.support.formData = false]Must close ajax, because when `this.support.formData` is false for some reason, directly use the form submit, rather than ajax
            // Yii::$app->request->enableCsrfValidation = false;    ///[csrf] no need to close csrf
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new UploadForm(['config' => $this->config]);

            if (Yii::$app->request->isPost) {

                $post = Yii::$app->request->post();
                $model->avatarSrc = $post['UploadForm']['avatarSrc'];
                $model->avatarData = $post['UploadForm']['avatarData'];
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                $result = $model->upload();

                ///[Yii2 cropper avatar:successCallback]
                ///Usually to save the avatar operation
                if ($this->successCallback) {
                    if (!is_callable($this->successCallback)) {
                        throw new InvalidConfigException('"' . get_class($this) . '::successCallback" should be a valid callback.');
                    }
                    ///[isInputWidget]tell action's successCallback not to save the avatar operation
                    ///[v0.10.5 (ADD# getParams())]
                    call_user_func($this->successCallback, $result, Yii::$app->request->get('isInputWidget'));
                }

                return $result;
            }
        // }
        return $this->controller->goHome();
    }
}