<?php

namespace Drupal\custom_fia\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'custom_fia_body' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_fia_body",
 *   label = @Translation("Custom FIA body"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
class CustomFiaBody extends FormatterBase {

  public function view(FieldItemListInterface $items, $langcode = NULL) {
    $elements = parent::view($items, $langcode);
    // Remove field div wrapper
    unset($elements['#theme']);
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'processed_text',
        '#text' => $item->value,
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];
    }
    return $elements;
  }

}
