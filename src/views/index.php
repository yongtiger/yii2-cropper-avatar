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

/**
 * @var $this yii\base\View
 * @var $form yii\widgets\ActiveForm
 * @var $model yongtiger\cropperavatar\models\UploadForm
 */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yongtiger\cropperavatar\AvatarWidget;

?>
<div class="container" id="crop-avatar">

    <!-- Current avatar -->
    <div class="avatar-view" title="<?= AvatarWidget::t('avatar', 'Change the avatar') ?>">

        <!--///[Yii2 cropper avatar]-->
        <!-- <img src="<?///= Yii::$app->assetManager->bundles['yongtiger\cropperavatar\AvatarAsset']->baseUrl . '/images/picture.jpg' ?>" alt="Avatar"> -->
        <!-- <img src="<?///= $this->assetBundles['yongtiger\cropperavatar\AvatarAsset']->baseUrl . '/images/picture.jpg' ?>" alt="Avatar"> -->
        <img src="<?= $this->context->noImageUrl ?>" alt="<?= AvatarWidget::t('avatar', 'Avatar') ?>">
        <!--///[http://www.brainbook.cc]-->

    </div>

    <!-- Cropping modal -->
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!--///[Yii2 cropper avatar]-->
                <!--///@see http://www.yiiframework.com/doc-2.0/guide-input-file-upload.html#rendering-file-input-->
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'avatar-form'],'action'=>['crop-avatar']]) ?>
                <!--///[http://www.brainbook.cc]-->

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="avatar-modal-label"><?= AvatarWidget::t('avatar', 'Change Avatar') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="avatar-body">

                        <!-- Upload image and data -->
                        <div class="avatar-upload">

                        <!--///[Yii2 cropper avatar]-->
                        <!-- <input type="hidden" class="avatar-src" name="avatar_src"> -->
                        <?= $form->field($model, 'avatarSrc')->hiddenInput(['class'=>'avatar-src'])->label(false) ?>
                        <!-- <input type="hidden" class="avatar-data" name="avatar_data"> -->
                        <?= $form->field($model, 'avatarData')->hiddenInput(['class'=>'avatar-data'])->label(false) ?>
                        <!-- <label for="avatarInput">Local upload</label> -->
                        <!-- <input type="file" class="avatar-input" id="avatarInput" name="avatar_file"> -->
                        <?= $form->field($model, 'imageFile')->fileInput(['class'=>'avatar-input', 'id'=>'avatarInput'])->label(AvatarWidget::t('avatar', 'Local upload')) ?>
                        <!--///[http://www.brainbook.cc]-->

                    </div>

                    <!-- Crop and preview -->
                    <div class="row">
                        <div class="col-md-9">
                            <div class="avatar-wrapper"></div>
                        </div>
                        <div class="col-md-3">

                            <!--///[Yii2 cropper avatar]-->
                            <?php if ($this->context->enablePreviewLargelImage): ?>
                                <div class="avatar-preview preview-lg"></div>
                            <?php endif; ?>
                            <?php if ($this->context->enablePreviewMiddlelImage): ?>
                                <div class="avatar-preview preview-md"></div>
                            <?php endif; ?>
                            <?php if ($this->context->enablePreviewSmalllImage): ?>
                                <div class="avatar-preview preview-sm"></div>
                            <?php endif; ?>
                            <!--///[http://www.brainbook.cc]-->

                        </div>
                    </div>

                    <div class="row avatar-btns">
                        <div class="col-md-9">

                            <!--///[Yii2 cropper avatar]-->
                            <?php if ($this->context->enableRotateButtons): ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-90" title="<?= AvatarWidget::t('avatar', 'Rotate -90 degrees') ?>"><?= AvatarWidget::t('avatar', 'Rotate Left') ?></button>
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-15">-15<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-30">-30<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45">-45<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                </div>
                                <div class="btn-group">
                                        <button type="button" class="btn btn-primary" data-method="rotate" data-option="90" title="<?= AvatarWidget::t('avatar', 'Rotate 90 degrees') ?>"><?= AvatarWidget::t('avatar', 'Rotate Right') ?></button>
                                        <button type="button" class="btn btn-primary" data-method="rotate" data-option="15">15<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                        <button type="button" class="btn btn-primary" data-method="rotate" data-option="30">30<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                        <button type="button" class="btn btn-primary" data-method="rotate" data-option="45">45<?= AvatarWidget::t('avatar', 'deg') ?></button>
                                </div>
                            <?php endif; ?>
                            <!--///[http://www.brainbook.cc]-->

                        </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-hyii btn-block avatar-save"><?= AvatarWidget::t('avatar', 'Done') ?></button>
                            </div>
                        </div>
                    </div>

                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div><!-- /.modal -->

    <!-- Loading state -->
    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
</div>
