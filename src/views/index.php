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
 * @var $form yii\bootstrap\ActiveForm
 * @var $model yongtiger\cropperavatar\models\UploadForm
 * @var $isInputWidget bool
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yongtiger\cropperavatar\AvatarWidget;

///[rounded avatar:image base64]
$isRounded && $this->registerCss(<<<CSS
    /* ///?????Override Cropper's styles */
    .cropper-view-box,.avatar-preview,
    .cropper-face {
        border-radius: 50%;
    }
CSS
);

?>
<!--///[isModal]///[InputWidget]///[rounded avatar:image base64]--><!--///Passing parameters to main.js-->
<div class="container <?= $isRounded ? 'is-rounded' : '' ?> <?= $isInputWidget ? 'is-input-widget' : '' ?> <?= $this->context->isModal ? 'is-modal' : '' ?>" id="crop-avatar">

    <!-- Current avatar -->
    <div class="avatar-view" title="<?= AvatarWidget::t('message', 'Change the avatar') ?>">

        <!--///[InputWidget]-->
        <img src="<?= $this->context->value ? : $this->context->noImageUrl ?>" alt="<?= AvatarWidget::t('message', 'Avatar') ?>">

    </div>
<!--///[http://www.brainbook.cc]-->

<!--///[isModal]-->
<?php if ($this->context->isModal): ?>
    <!-- Cropping modal -->
    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
<?php else: ?>
            <div id="avatar-modal">
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->

<!--///@see http://www.yiiframework.com/doc-2.0/guide-input-file-upload.html#rendering-file-input-->
<!--///[InputWidget]-->
<?php if ($isInputWidget): ?>
                <!-- <div class="avatar-form"> -->
                <?= Html::beginTag('div', ['class' => 'avatar-form', 'action' => Url::to(['crop-avatar', 'isInputWidget' => $isInputWidget])]); ?><!--///[isInputWidget]tell action's successCallback not to save the avatar operation-->
<?php else: ?>
                <?php $form = yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'avatar-form'],'action'=>['crop-avatar']]) ?>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->

<!--///[isModal]-->
<?php if ($this->context->isModal): ?>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="avatar-modal-label"><?= AvatarWidget::t('message', 'Change Avatar') ?></h4>
                    </div>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->

                    <div class="modal-body">
                        <div class="avatar-body">

                            <!-- Upload image and data -->
                            <div class="avatar-upload">

<!--///[InputWidget]-->
<?php if ($isInputWidget): ?>
                                <?= Html::csrfMetaTags() ?><!--///[csrf]@see http://stackoverflow.com/questions/32147040/csrf-issue-when-using-ajax-submit-via-link-in-yii2/32339079-->
                                <?= Html::hiddenInput('UploadForm[avatar_src]', $value = null, ['class'=>'avatar-src']); ?>
                                <?= Html::hiddenInput('UploadForm[avatar_data]', $value = null, ['class'=>'avatar-data']); ?>
                                <?= Html::fileInput('UploadForm[imageFile]', $value = null, ['class'=>'avatar-input', 'id'=>'avatarInput']); ?>
<?php else: ?>
                                <!-- <input type="hidden" class="avatar-src" name="avatar_src"> -->
                                <?= $form->field($model, 'avatarSrc')->hiddenInput(['class'=>'avatar-src'])->label(false) ?>
                                <!-- <input type="hidden" class="avatar-data" name="avatar_data"> -->
                                <?= $form->field($model, 'avatarData')->hiddenInput(['class'=>'avatar-data'])->label(false) ?>
                                <!-- <label for="avatarInput">Local upload</label> -->
                                <!-- <input type="file" class="avatar-input" id="avatarInput" name="avatar_file"> -->
                                <?= $form->field($model, 'imageFile')->fileInput(['class'=>'avatar-input', 'id'=>'avatarInput'])->label(AvatarWidget::t('message', 'Local upload')) ?>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->
                                
                            </div>

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
                                    <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="-90" title="<?= AvatarWidget::t('message', 'Rotate -90 degrees') ?>"><?= AvatarWidget::t('message', 'Rotate Left') ?></button>
                                    <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="-15">-15<?= AvatarWidget::t('message', 'deg') ?></button>
                                    <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="-30">-30<?= AvatarWidget::t('message', 'deg') ?></button>
                                    <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="-45">-45<?= AvatarWidget::t('message', 'deg') ?></button>
                                </div>
                                <div class="btn-group">
                                        <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="90" title="<?= AvatarWidget::t('message', 'Rotate 90 degrees') ?>"><?= AvatarWidget::t('message', 'Rotate Right') ?></button>
                                        <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="15">15<?= AvatarWidget::t('message', 'deg') ?></button>
                                        <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="30">30<?= AvatarWidget::t('message', 'deg') ?></button>
                                        <button type="button" class="btn btn-primary" data-trix-method="rotate" data-option="45">45<?= AvatarWidget::t('message', 'deg') ?></button>
                                </div>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->

                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-hyii btn-block avatar-save"><?= AvatarWidget::t('message', 'Done') ?></button>
                            </div>
                        </div>

                    </div>

<!--///[InputWidget]-->
<?php if ($isInputWidget): ?>
                </div>
<?php else: ?>
                <?php yii\bootstrap\ActiveForm::end() ?>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->
                
            </div>
<!--///[isModal]-->
<?php if ($this->context->isModal): ?>
        </div>
    </div><!-- /.modal -->
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->

    <!-- Loading state -->
    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
</div>

<!--///[InputWidget]input-widget-avatar-field-->
<?php if ($isInputWidget): ?>
<div id="input-widget-avatar-field" class="form-group" title="<?= AvatarWidget::t('message', 'Change the avatar') ?>">
    <?= $this->context->field->textInput() ?>
</div>
<?php endif; ?>
<!--///[http://www.brainbook.cc]-->