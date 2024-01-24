<?php

namespace Drupal\custom_fia\Plugin\views\row;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\file\Entity\File;
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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_manager, LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory) {
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
    $options['title'] = $entity->getTitle();
    $options['author'] = $entity->getOwner()->getAccountName();
    $options['created'] = '@'.$entity->getCreatedTime();
    $options['modified'] = '@'.$entity->getChangedTime();
    $options['link'] = $entity->toLink(NULL, 'canonical', ['absolute' => true]);
    $options['guid'] = $entity->uuid();

    // Field_ui fields
    if ($config->get('field_author') && $entity->hasField($config->get('field_author')) && $field = $entity->get($config->get('field_author'))->value) {
      $options['author'] = $field;
    }
    if ($config->get('field_deck') && $entity->hasField($config->get('field_deck')) && $field = $entity->get($config->get('field_deck'))->value) {
      $options['deck'] = $field;
    }
    if ($config->get('field_figure') && $entity->hasField($config->get('field_figure')) && $entity->get($config->get('field_figure'))->entity) {
      $media = $entity->get($config->get('field_figure'))->first()->get('entity')->getTarget()->getValue();
      $image = $media->get($config->get('field_figure_image'))->first()->get('entity')->getTarget()->getValue();
      $file = File::load($image->get('fid')->value);
      $options['figure'] = $file->createFileUrl(FALSE);
    }
    if ($config->get('field_kicker') && $entity->hasField($config->get('field_kicker')) && $field = $entity->get($config->get('field_kicker'))->value) {
      $options['kicker'] = $field;
    }
    if ($config->get('field_created') && $entity->hasField($config->get('field_created')) && $field = $entity->get($config->get('field_created'))->value) {
      $options['created'] = $field;
    }
    if ($config->get('field_subtitle') && $entity->hasField($config->get('field_subtitle')) && $field = $entity->get($config->get('field_subtitle'))->value) {
      $options['subtitle'] = $field;
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
