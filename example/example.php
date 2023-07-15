<?php

use TiagoF2\VuejsComponentFromSvg\Generator\GeneratorRunner;

require_once __DIR__ . '/vendor/autoload.php';

$initialClass = 'svg-icon-vue-component';
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
