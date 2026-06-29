<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class ModuleBoundaryTest extends TestCase
{
    private const MODULES_PATH = __DIR__ . '/../../modules';

    /**
     * Mapping of module names to namespaces that they must NOT import from.
     * Each module should only depend on Shared, not on other modules' internals.
     */
    private const FORBIDDEN_DEPENDENCIES = [
        'User' => ['Modules\\Order\\', 'Modules\\Catalog\\', 'Modules\\Payment\\'],
        'Order' => ['Modules\\User\\Domain\\', 'Modules\\User\\Infrastructure\\'],
        'Catalog' => ['Modules\\Order\\', 'Modules\\User\\'],
        'Payment' => ['Modules\\User\\', 'Modules\\Catalog\\'],
    ];

    #[Test]
    public function domain_layer_does_not_depend_on_infrastructure(): void
    {
        $modules = $this->getModuleDirectories();

        foreach ($modules as $module) {
            $domainPath = $module . '/Domain';

            if (! is_dir($domainPath)) {
                continue;
            }

            $files = $this->getPhpFiles($domainPath);

            foreach ($files as $file) {
                $content = file_get_contents($file->getPathname());
                $moduleName = basename($module);

                $this->assertStringNotContainsStringIgnoringCase(
                    'Illuminate\\',
                    $this->extractUseStatements($content),
                    "Domain layer in module '{$moduleName}' must not depend on Laravel framework. Found in: {$file->getPathname()}"
                );

                $this->assertStringNotContainsStringIgnoringCase(
                    'Infrastructure\\',
                    $this->extractUseStatements($content),
                    "Domain layer in module '{$moduleName}' must not depend on Infrastructure. Found in: {$file->getPathname()}"
                );
            }
        }
    }

    #[Test]
    public function application_layer_does_not_depend_on_infrastructure(): void
    {
        $modules = $this->getModuleDirectories();

        foreach ($modules as $module) {
            $applicationPath = $module . '/Application';

            if (! is_dir($applicationPath)) {
                continue;
            }

            $files = $this->getPhpFiles($applicationPath);

            foreach ($files as $file) {
                $content = file_get_contents($file->getPathname());
                $moduleName = basename($module);

                $this->assertStringNotContainsStringIgnoringCase(
                    'Infrastructure\\',
                    $this->extractUseStatements($content),
                    "Application layer in module '{$moduleName}' must not depend on Infrastructure. Found in: {$file->getPathname()}"
                );
            }
        }
    }

    #[Test]
    public function modules_do_not_cross_boundaries(): void
    {
        foreach (self::FORBIDDEN_DEPENDENCIES as $moduleName => $forbiddenNamespaces) {
            $modulePath = self::MODULES_PATH . '/' . $moduleName;

            if (! is_dir($modulePath)) {
                continue;
            }

            $files = $this->getPhpFiles($modulePath);

            foreach ($files as $file) {
                $content = file_get_contents($file->getPathname());
                $useStatements = $this->extractUseStatements($content);

                foreach ($forbiddenNamespaces as $forbiddenNs) {
                    $this->assertStringNotContainsString(
                        $forbiddenNs,
                        $useStatements,
                        "Module '{$moduleName}' must not depend on '{$forbiddenNs}'. Found in: {$file->getPathname()}"
                    );
                }
            }
        }
    }

    #[Test]
    public function shared_module_does_not_depend_on_other_modules(): void
    {
        $sharedPath = self::MODULES_PATH . '/Shared';

        if (! is_dir($sharedPath)) {
            $this->markTestSkipped('Shared module directory does not exist.');
        }

        $files = $this->getPhpFiles($sharedPath);
        $moduleNames = array_filter(
            array_map('basename', $this->getModuleDirectories()),
            static fn (string $name): bool => $name !== 'Shared',
        );

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $useStatements = $this->extractUseStatements($content);

            foreach ($moduleNames as $moduleName) {
                $this->assertStringNotContainsString(
                    "Modules\\{$moduleName}\\",
                    $useStatements,
                    "Shared module must not depend on module '{$moduleName}'. Found in: {$file->getPathname()}"
                );
            }
        }
    }

    /**
     * @return list<string>
     */
    private function getModuleDirectories(): array
    {
        if (! is_dir(self::MODULES_PATH)) {
            return [];
        }

        $directories = [];

        foreach (new \DirectoryIterator(self::MODULES_PATH) as $item) {
            if ($item->isDir() && ! $item->isDot()) {
                $directories[] = $item->getPathname();
            }
        }

        return $directories;
    }

    /**
     * @return list<SplFileInfo>
     */
    private function getPhpFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function extractUseStatements(string $content): string
    {
        preg_match_all('/^use\s+([^;]+);/m', $content, $matches);

        return implode("\n", $matches[1] ?? []);
    }
}
