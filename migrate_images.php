<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use Illuminate\Support\Facades\Storage;

echo "Starting image migration...\n";

// Ensure new directories exist
Storage::disk('public')->makeDirectory('eventpictures/posters');
Storage::disk('public')->makeDirectory('eventpictures/banners');

$events = Event::all();

foreach ($events as $event) {
    if ($event->poster_image && str_starts_with($event->poster_image, 'posters/')) {
        $oldPath = $event->poster_image;
        $newPath = str_replace('posters/', 'eventpictures/posters/', $oldPath);
        
        if (Storage::disk('public')->exists($oldPath)) {
            echo "Moving poster: $oldPath -> $newPath\n";
            Storage::disk('public')->move($oldPath, $newPath);
        }
        $event->poster_image = $newPath;
    }

    if (is_array($event->images)) {
        $newImages = [];
        foreach ($event->images as $img) {
            if (str_starts_with($img, 'posters/')) {
                $oldPath = $img;
                $newPath = str_replace('posters/', 'eventpictures/banners/', $oldPath);
                
                if (Storage::disk('public')->exists($oldPath)) {
                    echo "Moving banner: $oldPath -> $newPath\n";
                    Storage::disk('public')->move($oldPath, $newPath);
                }
                $newImages[] = $newPath;
            } else {
                $newImages[] = $img;
            }
        }
        $event->images = $newImages;
    }
    
    $event->save();
}

// Check if old posters directory is empty
if (Storage::disk('public')->exists('posters')) {
    $remainingFiles = Storage::disk('public')->files('posters');
    $remainingDirs = Storage::disk('public')->directories('posters');
    if (empty($remainingFiles) && empty($remainingDirs)) {
        Storage::disk('public')->deleteDirectory('posters');
        echo "Deleted old empty posters directory.\n";
    } else {
        echo "Old posters directory not empty, could not delete.\n";
    }
}

echo "Migration complete.\n";
