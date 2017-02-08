# Yii2 cropper avatar v0.4.0:successCallback


[![Latest Stable Version](https://poser.pugx.org/yongtiger/yii2-cropper-avatar/v/stable)](https://packagist.org/packages/yongtiger/yii2-cropper-avatar)
[![Total Downloads](https://poser.pugx.org/yongtiger/yii2-cropper-avatar/downloads)](https://packagist.org/packages/yongtiger/yii2-cropper-avatar) 
[![Latest Unstable Version](https://poser.pugx.org/yongtiger/yii2-cropper-avatar/v/unstable)](https://packagist.org/packages/yongtiger/yii2-cropper-avatar)
[![License](https://poser.pugx.org/yongtiger/yii2-cropper-avatar/license)](https://packagist.org/packages/yongtiger/yii2-cropper-avatar)


## Features

* Sample of extensions directory structure. `src`, `docs`, etc.
* `README.md`
* `composer.json`
* `development-roadmap.md`

## Dependences

* [Yii2 Advanced Template](https://github.com/yiisoft/yii2-app-advanced)


## Installation   

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yongtiger/yii2-cropper-avatar "*"
```

or add

```json
"yongtiger/yii2-cropper-avatar": "*"
```

to the require section of your composer.json.


## Configuration


## Usage


## Notes


## Documents


## See also


## TBD

* Yii::$app->request->enableCsrfValidation = false;    ///?????close csrf (https://github.com/yongtiger/yii2-cropper-avatar/blob/master/src/actions/CropAvatarAction.php#L42)
* [['avatarData', 'imageFile'], 'required'],///?????'avatarSrc' (https://github.com/yongtiger/yii2-cropper-avatar/blob/master/src/models/UploadForm.php#L102)
* 在activeForm里使用 (https://github.com/yidashi/yii2-webuploader), InputWidget (https://github.com/yidashi/yii2-webuploader)
* a-range-of-aspect-ratio (https://github.com/fengyuanchen/cropper/blob/master/examples/a-range-of-aspect-ratio.html)
* 用yii\web\UploadedFile的saveas保存上传图片！节省很多代码！换为imagine可以处理图片旋转
* 上传到qiniu等第三方图片云端


## [Development roadmap](docs/development-roadmap.md)


## License 
**yii2-cropper-avatar** is released under the MIT license, see [LICENSE](https://opensource.org/licenses/MIT) file for details.