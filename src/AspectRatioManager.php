<?php

namespace Drupal\css_aspect_ratio;

use Drupal\breakpoint\BreakpointManager;
use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;

/**
 * SubscriptionManager.
 */
class AspectRatioManager implements AspectRatioManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\breakpoint\BreakpointManagerInterface
   */
  private $breakpointManager;

  /**
   * Construct the SubscriptionManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, BreakpointManagerInterface $break_point_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->breakpointManager = $break_point_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getCSS() {
    $css = '';
    $groups = $this->breakpointManager->getGroups();
    $queries = [];
    // Fixed Aspect Ratio
    $css .= ".css-aspect-ratio {\n";
    $css .= "padding-bottom: var(--css-aspect-ratio);\n";
    $css .= "}\n";

    // Responsive Aspect Ratio
    foreach ($groups as $group => $group_label) {
      $css .= '.css-aspect-ratio--' . Html::cleanCssIdentifier($group) . " {\n";
      $breakpoints = $this->breakpointManager->getBreakpointsByGroup($group);
      foreach ($breakpoints as $breakpoint_name => $breakpoint) {
        $variable_name = Html::cleanCssIdentifier($breakpoint_name . '--css-aspect-ratio');
        if (!empty($breakpoint->getMediaQuery())) {
          $css .= "@media $breakpoint->getMediaQuery() {\n";
        }
        $css .= "padding-bottom: var(--$variable_name);\n";
        if (!empty($breakpoint->getMediaQuery())) {
          $css .= "}\n";;
        }
      }
      $css .= "}\n";
    }
    return $queries;
  }

  public function getPaddingBottom($width, $height) {
    return ($width / $height) * 100;
  }
}
