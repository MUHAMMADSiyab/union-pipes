<?php

namespace App\Services;


use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
  /*
     * Get the path for the given media, relative to the root storage path.
     */
  public function getPath(Media $media): string
  {
    if ($media->model_type === 'App\\Models\\Setting') {
      return 'settings/' . $media->id . '/';
    }
  }

  /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
  public function getPathForConversions(Media $media): string
  {
    if ($media->model_type === 'App\\Models\\Setting') {
      return 'settings/conversions/' . $media->id . '/';
    }
  }

  /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
  public function getPathForResponsiveImages(Media $media): string
  {
    if ($media->model_type === 'App\\Models\\Setting') {
      return 'settings/responsive-images/' . $media->id . '/';
    }
  }
}