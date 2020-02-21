<?php

namespace Drupal\Tests\ui_patterns_settings\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\ui_patterns\Traits\TwigDebugTrait;

/**
 * Test pattern preview rendering.
 *
 * @group ui_patterns_settings
 */
class UiPatternsSettingsRenderTest extends BrowserTestBase {

  /**
   * Disable schema validation when running tests.
   *
   * @var bool
   *
   * @todo: Fix this by providing actual schema validation.
   */
  protected $strictConfigSchema = FALSE;

  use TwigDebugTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'ui_patterns',
    'ui_patterns_ds',
    'ui_patterns_library',
    'ui_patterns_layouts',
    'ui_patterns_settings',
    'field_ui',
    'token',
    'ds',
    'ui_patterns_settings_render_test',
  ];

  /**
   * Tests pattern preview suggestions.
   */
  public function testRender() {

    $assert_session = $this->assertSession();

    $this->drupalCreateContentType(['type' => 'article']);
    $created_node = $this->drupalCreateNode(['title' => t('Hello Settings'), 'type' => 'article']);
    $this->enableTwigDebugMode();

    $user = $this->drupalCreateUser([], NULL, TRUE);
    $this->drupalLogin($user);

    // Define mapping for each setting type.
    $mappings = [
      '[textfield]' => ['input' => 'Text', 'result' => 'Textfield: Text'],
      '[number]' => ['input' => '10', 'result' => 'Number: 10'],
      '[token][input]' => ['input' => '[node:nid]', 'result' => 'Token: 1'],
      '[url][input]' => ['input' => 'internal:/node/1', 'result' => 'Url: /node/1'],
      '[boolean]' => ['input' => '1', 'result' => 'Boolean: 1'],
      '[select]' => ['input' => 'key', 'result' => 'Select: key'],
      '[checkbox][box1]' => ['input' => TRUE, 'result' => 'Checkbox: Box1'],
      '[attributes]' => ['input' => 'class="class"', 'result' => 'Attributes:  class="class"'],
    ];

    // Select the layout.
    $edit = [
      'ds_layout' => 'pattern_foo_settings',
    ];
    $this->drupalPostForm('/admin/structure/types/manage/article/display', $edit, 'Save');

    // Fill settings.
    $edit = [];
    foreach ($mappings as $key => $mapping) {
      $edit['layout_configuration[pattern][settings]' . $key] = $mapping['input'];
    }
    $this->drupalPostForm('/admin/structure/types/manage/article/display', $edit, 'Save');

    // Check values.
    $this->drupalGet('/node/' . $created_node->id());
    foreach ($mappings as $key => $mapping) {
      $assert_session->responseContains($mapping['result']);
    }
  }

}
