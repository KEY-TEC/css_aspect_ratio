<?php

namespace Drupal\Tests\css_aspect_ratio\Kernel;

use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * AspectRatioManagerTest.
 *
 * @group css_aspect_ratio
 */
class AspectRatioManagerTest extends KernelTestBase
{


  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'css_aspect_ratio',
    'responsive_image',
    'field',
    'image',
    'file',
    'entity_test',
    'breakpoint',
    'responsive_image_test_module',
    'user',
  ];

  /**
   * @var \Drupal\css_aspect_ratio\AspectRatioManagerInterface
   */
  private $aspectRatioManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
    parent::setUp();
    $this->installEntitySchema('entity_test');
    $this->aspectRatioManager = \Drupal::service('css_aspect_ratio.manager');
  }

  /**
   * Test testGetMediaQueries.
   */
  public function testGetMediaQueries()
  {
    ResponsiveImageStyle::create(
      [
        'id' => 'foo',
        'label' => 'Foo',
        'breakpoint_group' => 'responsive_image_test_module',
      ]
    )->save();
    $media_queries = $this->aspectRatioManager->getMediaQueries();
  }

}
