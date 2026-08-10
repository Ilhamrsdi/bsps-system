<?php

header('Content-Type: text/plain');

echo "=== FIXING HOSTINGER NGINX SYMLINK BLOCK ===\n\n";

$baseDir = dirname(__DIR__);
$target = $baseDir . '/storage/app/public/uploads';
$publicStorage = $baseDir . '/public/storage';
$publicUploads = $publicStorage . '/uploads';

// 1. Jika public/storage adalah symlink, hapus agar Nginx tidak nge-block 403 Forbidden
if (is_link($publicStorage)) {
    unlink($publicStorage);
    echo "Unlinked symlink: $publicStorage\n";
}

// 2. Buat folder FISIK asli public/storage/uploads
if (!file_exists($publicUploads)) {
    mkdir($publicUploads, 0755, true);
    echo "Created physical directory: $publicUploads\n";
}

// 3. Salin seluruh file foto dari storage/app/public/uploads ke public/storage/uploads
if (file_exists($target)) {
    $files = scandir($target);
    $copied = 0;
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            $src = $target . '/' . $f;
            $dst = $publicUploads . '/' . $f;
            if (is_file($src)) {
                copy($src, $dst);
                chmod($dst, 0644);
                $copied++;
            }
        }
    }
    echo "Successfully copied $copied files to physical folder public/storage/uploads!\n";
}

@chmod($publicStorage, 0755);
@chmod($publicUploads, 0755);

echo "\n=== FIX COMPLETE: REAL PHYSICAL FOLDER ACTIVE & NGINX 403 BYPASSED! ===\n";
