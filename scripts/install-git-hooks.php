<?php

/**
 * Installs the versioned git hooks (scripts/git-hooks/*) into .git/hooks.
 *
 * Runs automatically after `composer install` / `composer update` (see the
 * "post-install-cmd" / "post-update-cmd" entries in composer.json), so every
 * developer who sets up the project gets the hooks without a manual step.
 *
 * You can also run it directly at any time:
 *   php scripts/install-git-hooks.php
 */

$repoRoot = dirname(__DIR__);
$hooksDir = $repoRoot.'/.git/hooks';
$sourceDir = __DIR__.'/git-hooks';

if (! is_dir($repoRoot.'/.git')) {
    fwrite(STDERR, "Not a git repository — skipping git hook install.\n");
    exit(0);
}

if (! is_dir($sourceDir)) {
    fwrite(STDERR, "No scripts/git-hooks directory found — nothing to install.\n");
    exit(0);
}

if (! is_dir($hooksDir)) {
    mkdir($hooksDir, 0755, true);
}

$installed = 0;

foreach (glob($sourceDir.'/*') as $hookFile) {
    if (! is_file($hookFile)) {
        continue;
    }

    $name = basename($hookFile);
    $target = $hooksDir.'/'.$name;

    copy($hookFile, $target);

    // chmod is a no-op (but harmless) on Windows; Git for Windows' bundled
    // sh.exe runs the hook file directly regardless of the mode bit.
    @chmod($target, 0755);

    echo "Installed git hook: {$name}\n";
    $installed++;
}

if ($installed === 0) {
    echo "No hook files found in scripts/git-hooks.\n";
}
