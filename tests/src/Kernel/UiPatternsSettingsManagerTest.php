<?php

namespace Drupal\Tests\ui_patterns_settings\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ui_patterns\UiPatterns;
use Drupal\ui_patterns_settings\UiPatternsSettings;

/**
 * @coversDefaultClass \Drupal\ui_patterns_settings\UiPatternsSettingsManager
 *
 * @group ui_patterns
 */
class UiPatternsSettingsManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'ui_patterns',
    'ui_patterns_settings',
    'ui_patterns_library',
    'ui_patterns_settings_render_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Theme with existing patterns has to be enabled.
    $default_theme = 'ui_patterns_library_theme_test';
    $this->container->get('theme_installer')->install([$default_theme]);
    $this->container->get('config.factory')->getEditable('system.theme')->set('default', $default_theme)->save();
  }

  /**
   * Test UiPatternsSettingsManager::getExposedInfo.
   *
   * @covers ::getExposedInfo
   */
  public function testGetExposedInfo() {
    $manager = UiPatternsSettings::getManager();
    $defs = UiPatterns::getPatternDefinitions();
    $exposed_settings = $manager->getExposedInfo($defs);
    $this->assertTrue(isset($exposed_settings['foo_expose_settings']['settings']['select']));
    $this->assertTrue(isset($exposed_settings['foo_expose_settings']['variant']));
  }

}
