<?php

namespace TiagoF2\VuejsComponentFromSvg\Generator;

use Symfony\Component\Finder\Finder;

class GeneratorRunner
{
    protected $vueSvgIconComponentStubPath = null;

    public function __construct(
        protected string $svgSourcePath,
        protected ?string $outputDir,
        protected ?string $initialClass = null,
        protected ?string $componentPrefix = null,
        protected ?string $componentSufix = null,
        ?string $vueSvgIconComponentStubPath = null,
        protected array $replacers = [],
    ) {
        $this->vueSvgIconComponentStubPath = $vueSvgIconComponentStubPath ?: __DIR__ . '/../../stubs/VueIconComponent.stub';
    }

    /**
     * function getSvgSourcePath
     *
     * @return string
     */
    protected function getSvgSourcePath(): string
    {
        if (!$this->svgSourcePath || !is_dir($this->svgSourcePath)) {
            throw new \Exception('Invalid [svgSourcePath] directory.', 1);
        }

        return $this->svgSourcePath;
    }

    /**
     * function getOutputDir
     *
     * @param ?string $outputDir
     *
     * @return string
     */
    protected function getOutputDir(?string $outputDir = null): string
    {
        $outputDir ??= $this->outputDir;

        return $outputDir;
    }

    /**
     * function getStubContent
     *
     * @return string
     */
    protected function getStubContent(): string
    {
        if (!$this->vueSvgIconComponentStubPath) {
            throw new \Exception('Invalid "vueSvgIconComponentStubPath"');
        }

        if (!file_exists($this->vueSvgIconComponentStubPath)) {
            throw new \Exception("File not exists!\n File [{$this->vueSvgIconComponentStubPath}] not exists.");
        }

        return file_get_contents($this->vueSvgIconComponentStubPath);
    }

    /**
     * function generateFiles
     *
     * @param ?string $outputDir
     *
     * @return array
     */
    public function generateFiles(?string $outputDir = null): array
    {
        $outputDir = $this->getOutputDir($outputDir);

        // https://symfony.com/doc/current/components/finder.html#usage
        $finder = new Finder();

        $finder->name('*.svg');

        // find all files in the current directory
        $finder->files()->in($this->getSvgSourcePath());

        // check if there are any search results
        if (!$finder->hasResults()) {
            return [];
        }

        if (file_exists(($outputDir)) && !is_dir($outputDir)) {
            throw new \Exception("Invalid directory!\n[{$outputDir}] must be a directory.", 1);
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $stubContent = $this->getStubContent();

        foreach ($finder as $file) {
            $svgFileName = pathinfo($file->getRealPath(), PATHINFO_FILENAME);
            $componentFileName =  str($svgFileName)
                ->prepend(" {$this->componentPrefix} ")
                ->append(" {$this->componentSufix} ")
                ->studly()
                ->toString();

            $componentFilePath = "{$outputDir}/{$componentFileName}.vue";

            try {
                $svg = new \DOMDocument();
                $svg->load($file->getRealPath());

                if ($svg->firstChild->nodeName !== 'svg') {
                    $generatedFiles['errors'] = [
                        'sourceSVG' => "{$svgFileName}.svg",
                        'sourceSVGPath' => $file->getRealPath(),
                        'error' => 'Invalid svg nodeName',
                    ];

                    continue;
                }

                if (!method_exists($svg->firstChild, 'setAttribute')) {
                    $generatedFiles['errors'] = [
                        'sourceSVG' => "{$svgFileName}.svg",
                        'sourceSVGPath' => $file->getRealPath(),
                        'error' => 'Has no setAttribute method',
                    ];

                    continue;
                }

                if ($this->initialClass) {
                    $svg->firstChild->setAttribute('class', $this->initialClass);
                }

                $svg->firstChild->setAttribute(':class', '`w-${computedSize} h-${computedSize}`');

                $componentReplacer = $this->replacers;
                $componentReplacer['##SVG_CONTENT##'] = $svg->saveHTML();

                $componentContent = str_replace(
                    array_keys($componentReplacer),
                    array_values($componentReplacer),
                    $stubContent
                );

                file_put_contents($componentFilePath, $componentContent);

                $generatedFiles['success'] = [
                    'sourceSVG' => "{$svgFileName}.svg",
                    'sourceSVGPath' => $file->getRealPath(),
                    'componentName' => $componentFileName,
                    'componentFilePath' => $componentFilePath
                ];
            } catch (\Throwable $th) {
                $generatedFiles['errors'] = [
                    'sourceSVG' => "{$svgFileName}.svg",
                    'sourceSVGPath' => $file->getRealPath(),
                    'error' => $th->getMessage(),
                ];

                // throw $th;
            }
        }

        return $generatedFiles ?? [];
    }
}
