# vuejs-component-from-svg
> A package to easily generate Vue icon components from SVG files.

* [**Packagist URL**](https://packagist.org/packages/tiago-f2/vuejs-component-from-svg)

```sh
composer require tiago-f2/vuejs-component-from-svg
```

* If want to use dev version use `dev-master` tag
```sh
composer require tiago-f2/vuejs-component-from-svg:dev-master
```

## Example
* See [`example/example.php`](./example/example.php)

```php
use TiagoF2\VuejsComponentFromSvg\Generator\GeneratorRunner;

require_once __DIR__ . '/vendor/autoload.php';

$initialClass = 'svg-icon-vue-component'; // CSS class on SVG tag
$componentPrefix = 'Custom';
$componentSufix = 'Icon';
$svgSourcePath = __DIR__ . '/svg';
$outputDir = __DIR__ . '/dist/tiago';

$generatorRunner = new GeneratorRunner(
    $svgSourcePath,
    $outputDir,
    // $initialClass, // Optional
    // $componentPrefix, // Optional
    // $componentSufix, // Optional
);

$generatorRunner->generateFiles();

```

## [TODO](./TODO.md)
