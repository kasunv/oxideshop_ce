<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class TemplateCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private $templateCacheDirectory;

    /** @var Filesystem */
    private $filesystem;

    protected function setUp()
    {
        $this->filesystem = new Filesystem();
        $this->templateCacheDirectory = "/var/www/oxideshop/source/tmp/smarty";

        parent::setUp();
    }

    public function testInvalidateTemplateCache(): void
    {
        $this->putFilesInTemplateCache();

        $basicContext = $this->get(BasicContextInterface::class);
        $templateCacheService = new TemplateCacheService($basicContext, $this->filesystem);

        $templateCacheService->invalidateTemplateCache();

        self::assertCount(
            0,
            glob("{$this->templateCacheDirectory}/*.tpl.php")
        );
    }

    private function putFilesInTemplateCache(): void
    {
        $templates = [
            'c42586fe31b45ea1d3732c1fa5f8d84d^%%0F^0F2^0F24333D%%notice.tpl.php',
            'c42586fe31b45ea1d3732c1fa5f8d84d^%%28^28D^28DEE24D%%errors.tpl.php',
            'c42586fe31b45ea1d3732c1fa5f8d84d^%%F1^F1F^F1FCC4C0%%success.tpl.php'
        ];

        if (!$this->filesystem->exists($this->templateCacheDirectory)) {
            $this->filesystem->mkdir($this->templateCacheDirectory);
        }

        foreach ($templates as $template) {
            $this->filesystem->dumpFile(
                Path::join($this->templateCacheDirectory, $template),
                ""
            );
        }
    }
}
