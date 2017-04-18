<?php

namespace Drupal\daterange_compact\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Compact' formatter for 'daterange' fields.
 *
 * This formatter renders the data range using <time> elements, with
 * configurable date formats (from the list of configured formats) and a
 * separator.
 *
 * @FieldFormatter(
 *   id = "daterange_compact",
 *   label = @Translation("Compact"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class DateRangeCompactFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if (!empty($item->start_date) && !empty($item->end_date)) {

        /** @var string $start_value */
        $start_value = $item->value;
        /** @var string $end_value */
        $end_value = $item->end_value;

        /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
        $start_date = $item->start_date;
        /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
        $end_date = $item->end_date;

        // we always show the end date
        $output = Html::escape($end_date->format('j F Y'));

        if (substr($start_value, 0, 4) !== substr($end_value, 0, 4)) {
          // the years differ, so we need to show the full start date
          $output = Html::escape($start_date->format('j F Y')) . '&ndash;' . $output;
        }
        else if (substr($start_value, 0, 7) !== substr($end_value, 0, 7)) {
          // the months differ (but not the year), so we need to show the start day and month
          $output = Html::escape($start_date->format('j F')) . '&ndash;' . $output;
        }
        else if (substr($start_value, 0, 10) !== substr($end_value, 0, 10)) {
          // the days differ (but not the month), so we need to show the start day
          $output = Html::escape($start_date->format('j')) . '&ndash;' . $output;
        }

        $elements[$delta] = [
          '#markup' => $output,
        ];

      }
    }

    return $elements;
  }

}
