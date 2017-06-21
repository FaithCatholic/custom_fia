<?php

namespace Drupal\custom_fia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Settings.
 *
 * @package Drupal\custom_fia\Form
 */
class Settings extends ConfigFormBase {

  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_fia_settings';
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_fia.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('custom_fia.settings');

    $form['page_id'] = array(
      '#default_value' => $config->get('page_id') ? $config->get('page_id') : '',
      '#required' => TRUE,
      '#title' => 'FB Page ID',
      '#type' => 'textfield',
    );

    $view_modes = \Drupal::entityQuery('entity_view_mode')
      ->condition('targetEntityType', 'node')
      ->execute();

    $form['view_mode'] = array(
      '#default_value' => $config->get('view_mode') ? ('node.' . $config->get('view_mode')) : '',
      '#required' => TRUE,
      '#title' => 'Display view mode',
      '#type' => 'select',
      '#options' => $view_modes,
    );

    // Get defined text fields from node entities.
    $node_fields = [];
    $fields = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->loadByProperties(array(
        'entity_type' => 'node',
        'deleted' => FALSE,
        'status' => 1,
      ));
    foreach($fields as $field) {
      if ($field_id = $field->get('field_name')) {
        $node_fields[$field_id] = $field_id;
      }
    }

    $media_fields = [];
    $fields = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->loadByProperties(array(
        'entity_type' => 'media',
        'deleted' => FALSE,
        'status' => 1,
      ));
    foreach($fields as $field) {
      if ($field_id = $field->get('field_name')) {
        $media_fields[$field_id] = $field_id;
      }
    }

    $form['fields'] = array(
      '#title' => t('Field mappings'),
      '#type' => 'fieldset',
    );

    $form['fields']['field_author'] = array(
      '#default_value' => $config->get('field_author') ? $config->get('field_author') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Author field',
      '#type' => 'select',
    );

    $form['fields']['field_deck'] = array(
      '#default_value' => $config->get('field_deck') ? $config->get('field_deck') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Deck field',
      '#type' => 'select',
    );

    $form['fields']['field_kicker'] = array(
      '#default_value' => $config->get('field_kicker') ? $config->get('field_kicker') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Kicker field',
      '#type' => 'select',
    );

    $form['fields']['field_created'] = array(
      '#default_value' => $config->get('field_created') ? $config->get('field_created') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Created field',
      '#type' => 'select',
    );

    $form['fields']['field_subtitle'] = array(
      '#default_value' => $config->get('field_subtitle') ? $config->get('field_subtitle') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Subtitle field',
      '#type' => 'select',
    );

    $form['fields']['field_figure'] = array(
      '#default_value' => $config->get('field_figure') ? $config->get('field_figure') : '',
      '#empty_option' => 'Default',
      '#options' => $node_fields,
      '#required' => FALSE,
      '#title' => 'Figure reference field',
      '#type' => 'select',
    );

    $form['fields']['field_figure_image'] = array(
      '#default_value' => $config->get('field_figure_image') ? $config->get('field_figure_image') : '',
      '#empty_option' => 'Default',
      '#options' => $media_fields,
      '#required' => FALSE,
      '#title' => 'Figure referenced entity image field',
      '#type' => 'select',
    );

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {

      if ($key === 'view_mode') {
        $value = ltrim($value, 'node.');
      }

      $this->config('custom_fia.settings')->set($key, $value);
    }
    $this->config('custom_fia.settings')->save();
    parent::submitForm($form, $form_state);
  }

}
