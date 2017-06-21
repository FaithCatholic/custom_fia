<?php

namespace Drupal\custom_fia\Plugin\views\row;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\views\Plugin\views\row\EntityRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory) {
    $configuration['entity_type'] = 'node';
    $this->config = $config_factory;
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_manager, $language_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render($row) {
    GLOBAL $base_url;
    $entity = $row->_entity;
    $item = parent::render($row);
    $config = $this->config->get('custom_fia.settings');

    // Options fields.
    $options = $this->options;
    $options['row'] = $row;
    $options['langcode'] = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Default fields
    $options['title']     = $entity->getTitle();
    $options['author']    = $entity->getOwner()->getAccountName();
    $options['created']   = '@'.$entity->getCreatedTime();
    $options['modified']  = '@'.$entity->getChangedTime();
    $options['link']      = $entity->toLink(NULL, 'canonical', ['absolute' => true]);
    $options['guid']      = $entity->uuid();

    // Field_ui fields
    if ($config->get('field_author') && $entity->hasField($config->get('field_author')) && $value = $entity->get($config->get('field_author'))->value) {
      $options['author'] = $value;
    }
    if ($config->get('field_deck') && $entity->hasField($config->get('field_deck')) && $value = $entity->get($config->get('field_deck'))->value) {
      $options['deck'] = $value;
    }
    if ($config->get('field_figure') && $entity->hasField($config->get('field_figure')) && $image = $entity->get($config->get('field_figure'))->entity) {
      $options['figure'] = file_create_url($image->getFileUri());
    }
    if ($config->get('field_kicker') && $entity->hasField($config->get('field_kicker')) && $value = $entity->get($config->get('field_kicker'))->value) {
      $options['kicker'] = $value;
    }
    if ($config->get('field_created') && $entity->hasField($config->get('field_created')) && $value = $entity->get($config->get('field_created'))->value) {
      $options['created'] = $value;
    }
    if ($config->get('field_subtitle') && $entity->hasField($config->get('field_subtitle')) && $value = $entity->get($config->get('field_subtitle'))->value) {
      $options['subtitle'] = $value;
    }


    // if ($entity->hasField('field_featured_image'))  $options['figure']    = $entity->get('field_featured_image')->entity ? file_create_url($entity->get('field_featured_image')->entity->getFileUri()) : NULL;
    // if ($entity->hasField('field_kicker'))          $options['kicker']    = $entity->get('field_kicker')->value          ? $entity->get('field_kicker')->value : NULL;
    // if ($entity->hasField('field_published_date'))  $options['created']   = $entity->get('field_published_date')->value  ? $entity->get('field_published_date')->value : NULL;
    // if ($entity->hasField('field_subhead'))         $options['subtitle']  = $entity->get('field_subhead')->value         ? $entity->get('field_subhead')->value : NULL;

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
