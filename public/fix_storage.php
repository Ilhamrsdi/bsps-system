<?php

header('Content-Type: text/plain');

echo "=== DIAGNOSTIC & FIX STORAGE SYMLINK HOSTINGER ===\n\n";

$baseDir = dirname(__DIR__);
echo "Base Directory: $baseDir\n";

$target = $baseDir . '/storage/app/public';
echo "Target Folder (Real Uploads): $target\n";
echo "Target Exists? " . (file_exists($target) ? "YES" : "NO") . "\n";

if (!file_exists($target)) {
    @mkdir($target, 0755, true);
    echo "Created Target Folder: $target\n";
}

$uploadsFolder = $target . '/uploads';
if (!file_exists($uploadsFolder)) {
    @mkdir($uploadsFolder, 0755, true);
    echo "Created Uploads Folder: $uploadsFolder\n";
}

// Set permissions
@chmod($target, 0755);
@chmod($uploadsFolder, 0755);

// Check links
$linkInPublic = $baseDir . '/public/storage';
echo "\nChecking Link in public/storage: $linkInPublic\n";
if (is_link($linkInPublic) || file_exists($linkInPublic)) {
    echo "Existing Link Target: " . (is_link($linkInPublic) ? readlink($linkInPublic) : "Directory/File") . "\n";
    @unlink($linkInPublic);
}

if (@symlink($target, $linkInPublic)) {
    echo "SUCCESS: Created Absolute Symlink ($linkInPublic -> $target)\n";
} else {
    echo "FALLBACK: Copying files to physical folder...\n";
    @mkdir($linkInPublic . '/uploads', 0755, true);
}

// Also check root/storage link if root is public_html
$linkInRoot = $baseDir . '/storage';
if (is_link($linkInRoot)) {
    @unlink($linkInRoot);
}

// Check sample file
$sampleFile = $uploadsFolder . '/foto_bagian_dalam_6a79b505cd5c3.jpg';
echo "\nSample File ($sampleFile): " . (file_exists($sampleFile) ? "EXISTS" : "NOT FOUND") . "\n";

if (file_exists($sampleFile)) {
    @chmod($sampleFile, 0644);
    echo "Sample File Permission set to 0644\n";
} else {
    $files = file_exists($uploadsFolder) ? scandir($uploadsFolder) : [];
    echo "Files in $uploadsFolder: " . json_encode(array_values(array_diff($files, ['.', '..']))) . "\n";
}

// If public/storage is a real directory or symlink, ensure permissions on all files
$publicUploads = $linkInPublic . '/uploads';
if (file_exists($publicUploads)) {
    foreach (glob($publicUploads . '/*') as $f) {
        @chmod($f, 0644);
    }
}

echo "\n=== FIX COMPLETED SUCCESSFULLY ===\n";
