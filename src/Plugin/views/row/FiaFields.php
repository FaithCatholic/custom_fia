<?php

namespace Drupal\custom_fia\Plugin\views\row;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\views\Plugin\views\row\EntityRow;

/**
 * @ViewsRow(
 *   id = "custom_fiafields",
 *   title = @Translation("Custom FIA fields"),
 *   help = @Translation("Render content as Facebook instant articles."),
 *   theme = "custom_views_view_row_fia",
 *   display_types = {"feed"}
 * )
 */

class FiaFields extends EntityRow {

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager) {
    $configuration['entity_type'] = 'node';
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_manager, $language_manager);
  }

  public function render($row) {
    GLOBAL $base_url;
    $entity = $row->_entity;
    $item = parent::render($row);
    $options = $this->options;
    $options['langcode'] = \Drupal::languageManager()->getCurrentLanguage()->getId();

    switch (true) {
      default:
      case ($entity instanceof Node):
        $options['row'] = $row;

        // Default fields
        $options['title']     = $entity->getTitle();
        $options['author']    = $entity->getOwner()->getAccountName();
        $options['created']   = '@'.$entity->getCreatedTime();
        $options['modified']  = '@'.$entity->getChangedTime();
        $options['link']      = $entity->toLink(NULL, 'canonical', ['absolute'=>true]);
        $options['guid']      = $entity->uuid();
        $options['author']    = $entity->getOwner()->toLink(NULL,'canonical',['absolute'=>true]);

        // Field_ui fields
        if ($entity->hasField('field_byline'))          $options['author']    = $entity->get('field_byline')->value;
        if ($entity->hasField('field_published_date'))  $options['created']   = $entity->get('field_published_date')->value;
        if ($entity->hasField('field_byline'))          $options['author']    = $entity->get('field_byline')->value;
        if ($entity->hasField('field_subhead'))         $options['subtitle']  = $entity->get('field_subhead')->value;
        if ($entity->hasField('field_deckhead'))        $options['deck']      = $entity->get('field_deckhead')->value;

        // Featured image
        if ($entity->hasField('field_featured_image')) {
          $image = $entity->get('field_featured_image')->entity;
          $options['figure'] = '<img src="' . file_create_url($image->getFileUri()) . '">';
        }

    }

    $build = [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $options,
      '#row' => $item,
      '#field_alias' => isset($this->field_alias) ? $this->field_alias : '',
    ];

    return $build;
  }

}
