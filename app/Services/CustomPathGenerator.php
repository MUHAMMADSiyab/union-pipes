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

    if ($media->model_type === 'App\\Models\\Payment') {
      return 'payments/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\BulkPayment') {
      return 'bulk_payments/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Company') {
      return 'companies/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Customer') {
      return 'customers/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Partner') {
      return 'partners/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Employee') {
      return 'employees/' . $media->id . '/';
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

    if ($media->model_type === 'App\\Models\\Payment') {
      return 'payments/conversions/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\BulkPayment') {
      return 'bulk_payments/conversions/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Company') {
      return 'companies/conversions/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Customer') {
      return 'customers/conversions/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Partner') {
      return 'partners/conversions/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Employee') {
      return 'employees/conversions/' . $media->id . '/';
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

    if ($media->model_type === 'App\\Models\\Payment') {
      return 'payments/responsive-images/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\BulkPayment') {
      return 'bulk_payments/responsive-images/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Company') {
      return 'companies/responsive-images/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Customer') {
      return 'customers/responsive-images/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Partner') {
      return 'partners/responsive-images/' . $media->id . '/';
    }

    if ($media->model_type === 'App\\Models\\Employee') {
      return 'employees/responsive-images/' . $media->id . '/';
    }
  }
}
