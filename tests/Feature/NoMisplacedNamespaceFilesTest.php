<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Regression test for a real, confirmed bug found via a security sweep:
 * three files sat in app/Models/ despite declaring
 * `namespace App\Http\Controllers\Web` inside them — exact or
 * near-duplicate copies of real controllers, misplaced. Two were byte-
 * identical dead weight (harmless until something autoloaded them, which
 * is exactly what happened the moment this sweep script did). The third
 * (GuestController) was NOT a harmless duplicate: it contained the only
 * working copies of events()/eventShow(), which two LIVE routes
 * ('/ibikorwa', '/ibikorwa/{slug}') actually reference — meaning those
 * routes have been throwing a fatal "call to undefined method" error
 * this entire time, since PSR-4 autoloading always resolves
 * App\Http\Controllers\Web\GuestController to the file at its correct
 * path, never to a stray file elsewhere claiming the same namespace.
 * Fixed by merging the missing methods into the real file and deleting
 * the stray copies. This test makes sure the bug class — any file whose
 * declared namespace doesn't match its actual directory — can't
 * silently reappear.
 */
class NoMisplacedNamespaceFilesTest extends TestCase
{
    public function test_every_php_file_declares_a_namespace_matching_its_directory(): void
    {
        $appRoot = base_path('app');
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($appRoot, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativeDir = trim(str_replace($appRoot, '', $file->getPath()), DIRECTORY_SEPARATOR);
            $expectedNamespace = 'App'.($relativeDir ? '\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relativeDir) : '');

            $contents = file_get_contents($file->getPathname());
            if (! preg_match('/^namespace\s+([^;]+);/m', $contents, $matches)) {
                continue; // no namespace declared at all — not this bug class
            }

            $declaredNamespace = trim($matches[1]);

            if ($declaredNamespace !== $expectedNamespace) {
                $violations[] = "{$file->getPathname()}: declares '{$declaredNamespace}' but its location implies '{$expectedNamespace}'";
            }
        }

        $this->assertEmpty(
            $violations,
            "Found file(s) with a namespace that doesn't match their directory (the exact bug class that made two live guest routes fatal-error): \n".implode("\n", $violations)
        );
    }
}
