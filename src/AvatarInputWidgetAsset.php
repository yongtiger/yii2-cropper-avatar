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
use yii\web\AssetBundle;

/**
 * Class AvatarInputWidgetAsset
 *
 * @package yongtiger\AvatarInputWidgetAsset
 */
class AvatarInputWidgetAsset extends AssetBundle
{
    public $sourcePath = '@yongtiger/cropperavatar/assets';

    public $js = [
        'js/ajaxfileupload.js', ///[InputWidget]///[ajaxfileupload]
    ];
    
    public $depends = [
        'yongtiger\cropperavatar\AvatarAsset',
    ];
}