<?php

namespace Drupal\css_aspect_ratio;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_product_bundle\Entity\BundleInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * SubscriptionManagerInterface handles subscription operations.
 */
interface AspectRatioManagerInterface {

  /**
   * Returns the aspect ratio variables.
   *
   * @return int
   *   ...
   */
  public function getCSS();

  public function getPaddingBottom($width, $height);
}
