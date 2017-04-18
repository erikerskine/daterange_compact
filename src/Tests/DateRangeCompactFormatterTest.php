<?php

namespace Drupal\daterange_compact\Tests;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests compact date range field formatter functionality.
 *
 * @group field
 */
class DateRangeCompactFormatterTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system', 'field', 'datetime', 'datetime_range', 'daterange_compact', 'entity_test', 'user'];

  /**
   * @var string
   */
  protected $entityType;

  /**
   * @var string
   */
  protected $bundle;

  /**
   * @var string
   */
  protected $fieldName;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $display;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(['system']);
    $this->installConfig(['field']);
    $this->installEntitySchema('entity_test');

    $this->entityType = 'entity_test';
    $this->bundle = $this->entityType;
    $this->fieldName = Unicode::strtolower($this->randomMachineName());

    $field_storage = FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'type' => 'daterange',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATE,
      ],
    ]);
    $field_storage->save();

    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $this->bundle,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();

    $this->display = entity_get_display($this->entityType, $this->bundle, 'default')
      ->setComponent($this->fieldName, [
        'type' => 'daterange_compact',
        'settings' => [
          'timezone_override' => '',
          'separator' => '-',
          'format_type' => 'medium',
        ],
      ]);
    $this->display->save();
  }

  /**
   * Renders fields of a given entity with a given display.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity object with attached fields to render.
   * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   *   The display to render the fields in.
   *
   * @return string
   *   The rendered entity fields.
   */
  protected function renderEntityFields(FieldableEntityInterface $entity, EntityViewDisplayInterface $display) {
    $content = $display->build($entity);
    $content = $this->render($content);
    return $content;
  }

  /**
   * Tests DateRangeCompactFormatter.
   */
  function testCompactFormatter() {
    $all_data = [];
    $all_data[] = ['start' => '2017-01-01', 'end' => '2017-01-01', 'expected' => '1 January 2017'];
    $all_data[] = ['start' => '2017-01-02', 'end' => '2017-01-03', 'expected' => '2&ndash;3 January 2017'];
    $all_data[] = ['start' => '2017-01-04', 'end' => '2017-02-05', 'expected' => '4 January&ndash;5 February 2017'];
    $all_data[] = ['start' => '2017-01-06', 'end' => '2018-02-07', 'expected' => '6 January 2017&ndash;7 February 2018'];

    foreach ($all_data as $data) {
      $entity = EntityTest::create([]);
      $entity->{$this->fieldName}->value = $data['start'];
      $entity->{$this->fieldName}->end_value = $data['end'];

      $this->renderEntityFields($entity, $this->display);
      $this->assertRaw($data['expected']);
    }

  }



}

