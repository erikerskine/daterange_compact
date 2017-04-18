<?php

namespace Drupal\daterange_compact\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the date range format entity.
 *
 * @ConfigEntityType(
 *   id = "date_range_format",
 *   label = @Translation("Date range format"),
 *   config_prefix = "date_range_format",
 *   admin_permission = "administer site configuration",
 *   list_cache_tags = { "rendered" },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class DateRangeFormat extends ConfigEntityBase implements DateRangeFormatInterface {

  /**
   * The Date range format ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Date range format label.
   *
   * @var string
   */
  protected $label;

  /**
   * {@inheritdoc}
   */
  public function getDateSettings() {
    return $this->get('date_settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getDateTimeSettings() {
    return $this->get('datetime_settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    return ['rendered'];
  }

}
