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

## Using