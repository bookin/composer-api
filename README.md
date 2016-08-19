This is a pack of functions for interfacing with composer
============================

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/). 

To install, either run

```
$ php composer.phar require bookin/composer-api "@dev"
```

or add

```
"bookin/composer-api": "@dev"
```

to the ```require``` section of your `composer.json` file.


## Usage

Initialization
```
$composer = Composer::getInstance('path/to/root/composer.json', 'path/to/root');
```

Returns Composer\Composer object
```
$composer::getComposer();
```

Returns array with PackageInterface objects (array withh all installed packages, without bower, npm, etc from `fxp/composer-asset-plugin`)
```
$composer::getLocalPackages();
```

Find package by full name and version
```
$composer::findPackage($name, $version);
```

Find package by string
```
$composer::searchPackage($query);
```

Update package by name or all packages with [console options](https://getcomposer.org/doc/03-cli.md#update)
```
$composer::updatePackage($name, $options);
$composer::updateAllPackages($options);
```

Delete package by name or all packages with [console options](https://getcomposer.org/doc/03-cli.md#remove)
```
$composer::deletePackage($name, $options);
$composer::deleteAllPackages($options);
```

Run any composer [commands](https://getcomposer.org/doc/03-cli.md)
```
$composer::runCommand($command, $options);
```


###Example

You can see the work of the component on the example of yii2 module - [bookin/yii2-composer-gui](https://github.com/bookin/yii2-composer-gui)