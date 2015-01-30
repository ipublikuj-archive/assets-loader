# Assets loader

[![Build Status](https://img.shields.io/travis/iPublikuj/assets-loader.svg?style=flat-square)](https://travis-ci.org/iPublikuj/assets-loader)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/assets-loader.svg?style=flat-square)](https://packagist.org/packages/ipub/assets-loader)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/assets-loader.svg?style=flat-square)](https://packagist.org/packages/ipub/assets-loader)

Tool for loading static CSS and JS files for [Nette Framework](http://nette.org/)

## Installation

The best way to install ipub/assets-loader is using  [Composer](http://getcomposer.org/):

```json
{
	"require": {
		"ipub/assets-loader": "dev-master"
	}
}
```

or

```sh
$ composer require ipub/assets-loader:@dev
```

After that you have to register extension in config.neon.

```neon
extensions:
	assetsLoader: IPub\AssetsLoader\DI\AssetsLoaderExtension
```

Package contains trait, which you will have to use in presenter to implement Assets Loader factory into Presenter. This works only for PHP 5.3+, for older version you can simply copy trait content and paste it into class where you want to use it.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{

	use IPub\AssetsLoader\TAssetsLoader;

}
```

## Configuration

```neon
	# Static files loader
	assetsLoader:
		routes:
			# Here you can define your route for static files (*.css and *.js)
			assets	: "assets-files/<id>[-t<timestamp>][-<gzipped>].<type>"
			# And here you can define route for images or other files which can't be inserted directly into static file
			# eg. fonts in CSS files, or SVG images
			files	: "assets-files-images/<id>[-t<timestamp>]"
		css:
			gzip	: true # Enable or disable output gzip
			files	:
				# Here define all css static files which should be inserted into default asset
				- %fullPathToFile%/first.css
				- %fullPathToFile%/second.css
				- http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.css
			filters:
				# File filters (cssUrlsFilter, lessFilter)
				# You can also insert your custom filter as service 
				files	: ['cssUrlsFilter', @userCustomFilter]
				# Content filters (styleCompressor, cssImportFilter)
				# You can also insert your custom filter as service
				content	: ['styleCompressor', 'cssImportFilter']
		js:
			gzip	: true # Enable or disable output gzip
			files	:
				# Here define all js static files which should be inserted into default asset
				- %fullPathToFile%/first.js
				- %fullPathToFile%/second.js
				- http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js
			filters:
				# File filters (coffeScriptFilter)
				# You can also insert your custom filter as service 
				files	: ['coffeScriptFilter', @userCustomFilter]
				# Content filters (scriptCompressor)
				# You can also insert your custom filter as service
				content	: ['scriptCompressor']
```

This extension has also ability to define different assets groups. So you can define assets for your frontend and for your backend separately like this:

```neon
	# Static files loader
	assetsLoader:
		assets:
			# Define asset name
			# String 'default/Default' is deprecated
			frontend:
				css:
					gzip	: true # Enable or disable output gzip
					files	:
						# Here define all css static files which should be inserted into default asset
						- %fullPathToFile%/first.css
						- %fullPathToFile%/second.css
						- http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.css
					filters:
						# File filters (cssUrlsFilter, lessFilter)
						# You can also insert your custom filter as service 
						files	: ['cssUrlsFilter', @userCustomFilter]
						# Content filters (styleCompressor, cssImportFilter)
						# You can also insert your custom filter as service
						content	: ['styleCompressor', 'cssImportFilter']
				js:
					gzip	: true # Enable or disable output gzip
					files	:
						# Here define all js static files which should be inserted into default asset
						- %fullPathToFile%/first.js
						- %fullPathToFile%/second.js
						- http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js
					filters:
						# File filters (coffeScriptFilter)
						# You can also insert your custom filter as service 
						files	: ['coffeScriptFilter', @userCustomFilter]
						# Content filters (scriptCompressor)
						# You can also insert your custom filter as service
						content	: ['scriptCompressor']
```

And also this extension bring another feature **Packages**. So now you can define packages and this packages can be inserted into assets groups:

```neon
	common:
		parameters:
			assetsLoader:
				packages:
					# jQuery
					jQuery:
						# jQuery core
						core:
							js:
								- %staticFilesDir%/js/jquery/core/jquery-2.1.1.js
								- %staticFilesDir%/js/jquery/core/jquery.add.js
						# jQuery scroll to
						scrollTo:
							js:
								- %staticFilesDir%/js/jquery/addons/jquery-scrollTo/jquery-scrollTo.js
					# Bootstrap theme
					boostrap3:
						core:
							css:
								- %staticFilesDir%/themes/bootstrap3/css/bootstrap.css
							js:
								- %staticFilesDir%/themes/bootstrap3/js/bootstrap.js
					# Bootstrap theme icons
					icons:
						css:
							- %staticFilesDir%/themes/bootstrap3/icons/font-awesome.css
```

Now we have defined several packages and this packages can be used in all or some assets groups:

```neon
	# Static files loader
	assetsLoader:
		assets:
			# Frontend module or other name
			frontend:
				css:
					gzip		: true
					filters		:
						files	: ["cssUrlsFilter", "lessFilter"]
						content	: ["cssImportFilter"]
				js:
					gzip		: true
					filters		:
						files	: []
						content	: ["scriptCompressor"]
				packages:
					- %assetsLoader.packages.jQuery.core%
					- %assetsLoader.packages.jQuery.scrollTo%
					- %assetsLoader.packages.boostrap3.core%
					- %assetsLoader.packages.boostrap3.icons%
```

## Usage in PHP files

Now you have to create components in your presenter. This components will server static files HTML elements. So in presenter just create two components:

```php
class SomePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * CSS static files component
	 *
	 * @return \IPub\AssetsLoader\Components\CssLoader
	 */
	protected function createComponentCss()
	{
		return $this->assetsLoader->createCssLoader('yourAssetName');
	}

	/**
	 * JS static files component
	 *
	 * @return \IPub\AssetsLoader\Components\JsLoader
	 */
	protected function createComponentJs()
	{
		return $this->assetsLoader->createJsLoader('yourAssetName');
	}
}
```

Only one think what you have to set to **createXXLoader()** method is your asset name. If you don't use assets in groups, extension will use **default** asset. If you are using separated asset you can enter eg. **frontend**.

So this is all in PHP part. Extension is now fully integrated.

## Using in Latte

In your layout latte template you can put components calls:

```html
<html lang="en">
<head>
	{control css}
</head>
<body>
	{control js}
</body>
</html>
```

## Available filters

### File filters

#### coffeScriptFilter

Filter usable only for JavaScript files. This filter will compile your CoffeScripts into executable JS.

#### lessFilter

Filter usable only for CSS files. This filter will compile your Less files into clean CSS files.

#### cssUrlsFilter

Filter usable only for CSS files. This filter will convert all images into base64 encoded string, or if it is not a valid image for encoding, it will be converted to url created with **routes.files** route definition.

### Content filters

#### scriptCompressor

Filter usable only for JavaScript files. This filter minify a Javascript string

#### styleCompressor

Filter usable only for CSS files. This filter minify a CSS string

#### cssImportFilter

Filter usable only for CSS files. This filter move import to the top of the document

## Using IStaticFilesProvider

This extension also implement an interface for your extensions to automatically load static files. To use this functionality you have to implement IStaticFilesProvider interface into your extension and also one method **getStaticFiles**:

```php
class YourCoolExtension extends \IPub\AssetsLoader\DI\IStaticFilesProvider
{
	/**
	 * Return array of styles & scripts files
	 *
	 * @return array
	 */
	public function getStaticFiles()
	{
		return array(
			'frontend'	=> array(
				'css'	=> array(
					__DIR__ . '/../../../../client-side/frontend/css/style.css'
				),
				'js'	=> array(
					__DIR__ . '/../../../../client-side/frontend/js/nette.js',
				),
			),
			'panel'	=> array(
				'css'	=> array(
					__DIR__ . '/../../../../client-side/panel/css/style.css'
				),
				'js'	=> array(
					__DIR__ . '/../../../../client-side/panel/js/nette.js',
				),
			),
		);
	}
}
```

This static files provider copy the behaviour like in normal settings, so you can define here assets group or only define default asset.