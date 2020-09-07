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
    // Fixed Aspect Ratio
    $css .= ".css-aspect-ratio {\n";
    $css .= "padding-top: var(--css-aspect-ratio-padding-top);\n";
    $css .= "}\n";

    // Responsive Aspect Ratio
    foreach ($groups as $group => $group_label) {
      $css_class_name = '.css-aspect-ratio--' . Html::cleanCssIdentifier($group);
      $css .= $css_class_name . " {\n";
      $css .= "position: relative;\n";
      $css .= "width: 100%;\n";
      $css .= "}\n";

      $breakpoints = $this->breakpointManager->getBreakpointsByGroup($group); bist
      foreach ($breakpoints as $breakpoint_name => $breakpoint) {
        $variable_name = Html::cleanCssIdentifier($breakpoint_name . '--css-aspect-ratio');
        if (!empty($breakpoint->getMediaQuery())) {
          $media_breakpoint = $breakpoint->getMediaQuery();
          $css .= "@media $media_breakpoint {\n";
        }
        $css .= $css_class_name. "{\n";
        $css .= "padding-bottom: var(--$variable_name);\n";
        $css .= "}\n";
        if (!empty($breakpoint->getMediaQuery())) {
          $css .= "}\n";;
        }
      }
      $css .= "}\n";
    }
    return $css;
  }

  public function getPaddingBottom($width, $height) {
    return ($height / $width) * 100;
  }

  public function getResponsivePrefix($responsive_image_style, $responsive_image) {
    $responsive_prefix = [];
    $image_style_mapping = $responsive_image_style->getKeyedImageStyleMappings();
    foreach ($image_style_mapping as $image_style) {
      $current_pixel_ratio = current($image_style);
      if ($current_pixel_ratio != NULL) {
      $loaded_image_style = \Drupal\image\Entity\ImageStyle::load($current_pixel_ratio['image_mapping']);
      $dimensions = [
        "width" => $responsive_image['#width'],
        "height" => $responsive_image['#height']
      ];
      $loaded_image_style->transformDimensions($dimensions, $responsive_image['#uri']);
      $aspect_ratio = $dimensions['height'] / $dimensions['width'] * 100;
      $css_prefix = $current_pixel_ratio['breakpoint_id'];
      $variable_name = Html::cleanCssIdentifier($css_prefix . '--css-aspect-ratio');
      $responsive_prefix += [
        $variable_name => $aspect_ratio
      ];
    } else {
        // @TODO
      }
    }

    return $responsive_prefix;
  }
}
