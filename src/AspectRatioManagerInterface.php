<?php

namespace Drupal\css_aspect_ratio;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_product_bundle\Entity\BundleInterface;

/**
 * AspectRatioManagerInterface operations.
 */
interface AspectRatioManagerInterface {

  /**
   * Generates aspect ratio CSS based breakpoint informations.
   *
   * @return string
   *   The generated CSS.
   */
  public function getCSS();

  /**
   * Returns the calculated padding.
   *
   * @param $width
   *   The width of the image.
   * @param $height
   *   The height of the image.
   * @return mixed
   */
  public function getPaddingBottom($width, $height);
}
