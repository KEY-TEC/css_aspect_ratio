<?php

namespace Drupal\css_aspect_ratio;

use Drupal\breakpoint\BreakpointManager;
use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\image\Entity\ImageStyle;

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

    // Responsive Aspect Ratio
    foreach ($groups as $group => $group_label) {
      $css_class_name = '.css-aspect-ratio--' . Html::cleanCssIdentifier($group);
      $css .= $css_class_name . " {\n";
      $css .= "position: relative;\n";
      $css .= "width: 100%;\n";
      $css .= "overflow: hidden;\n";
      $css .= "padding-bottom: var(--css-aspect-ratio);\n";
      $css .= "}\n";

      $breakpoints = $this->breakpointManager->getBreakpointsByGroup($group);
      foreach ($breakpoints as $breakpoint_name => $breakpoint) {
        $variable_name = Html::cleanCssIdentifier($breakpoint_name . '--css-aspect-ratio');
        if (!empty($breakpoint->getMediaQuery())) {
          $media_breakpoint = $breakpoint->getMediaQuery();
          $css .= "@media $media_breakpoint {\n";
        }
        $css .= $css_class_name . "{\n";
        $css .= "padding-bottom: var(--$variable_name);\n";
        $css .= "}\n";
        if (!empty($breakpoint->getMediaQuery())) {
          $css .= "}\n";;
        }
      }
    }
    return $css;
  }

  public function getPaddingBottom($width, $height) {
    return ($height / $width) * 100;
  }

  public function getResponsivePrefix(ResponsiveImageStyle $responsive_image_style, $responsive_image) {
    $responsive_prefix = [];
    $dimensions = [
      "width" => $responsive_image['#width'],
      "height" => $responsive_image['#height'],
    ];

    $fallback_ratio = NULL;
    if ($responsive_image_style->getFallbackImageStyle() !== NULL) {
      $fallback_image_style = ImageStyle::load($responsive_image_style->getFallbackImageStyle());
      $fallback_image_style->transformDimensions($dimensions, $responsive_image['#uri']);
      $aspect_ratio = $this->getPaddingBottom($dimensions['width'], $dimensions['height']);
      $variable_name = Html::cleanCssIdentifier('css-aspect-ratio');
      $fallback_ratio = $aspect_ratio;
      $responsive_prefix += [
        $variable_name => $aspect_ratio,
      ];
    }

    $breakpoints = $this->breakpointManager->getBreakpointsByGroup($responsive_image_style->getBreakpointGroup());
    $image_style_mapping = $responsive_image_style->getKeyedImageStyleMappings();
    foreach ($breakpoints as $breakpoint_name => $breakpoint) {
      $variable_name = Html::cleanCssIdentifier($breakpoint_name . '--css-aspect-ratio');
      if (isset($image_style_mapping[$breakpoint_name])) {

        // Set default candiate to fallback;
        $image_style_candidate = $responsive_image_style->getFallbackImageStyle();
        $image_style = $image_style_mapping[$breakpoint_name];
        $current_pixel_ratio = current($image_style);
        if ($current_pixel_ratio != NULL) {
          // For mapping type sizes we use the first element.
          // All sizes should use the same aspect ratio.
          if ($current_pixel_ratio['image_mapping_type'] === 'sizes') {
            if ($current_pixel_ratio['image_mapping']['sizes_image_styles'][0]) {
              $image_style_candidate = $current_pixel_ratio['image_mapping']['sizes_image_styles'][0];
            }
          }
          else {
            $image_style_candidate = $current_pixel_ratio['image_mapping'];
          }
          $loaded_image_style = ImageStyle::load($image_style_candidate);
          if ($loaded_image_style !== NULL) {
            $loaded_image_style->transformDimensions($dimensions, $responsive_image['#uri']);
            $aspect_ratio = $this->getPaddingBottom($dimensions['width'], $dimensions['height']);
            $responsive_prefix += [
              $variable_name => $aspect_ratio,
            ];
          }
          else {
            // @TODO
            $x = 0;
          }
        }
      }
      else {
        $responsive_prefix += [
          $variable_name => $fallback_ratio,
        ];
      }
    }
    return $responsive_prefix;
  }

}
