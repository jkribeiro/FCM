<?php

namespace Drupal\fcm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for FCM.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['fcm.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fcm_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('fcm.settings');

    $form['settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Settings'),
      '#description' => $this->t('Set the configuration provided by Firebase, more details: https://firebase.google.com/docs/web/setup'),
    ];

    $form['settings']['server_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Server key'),
      '#description' => $this->t('Google server key: https://console.developers.google.com/apis/credentials'),
      '#default_value' => $config->get('server_key'),
      '#required' => TRUE,
    ];

    $form['settings']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];

    $form['settings']['project_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project ID'),
      '#default_value' => $config->get('project_id'),
      '#required' => TRUE,
    ];

    $form['settings']['auth_domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Auth domain'),
      '#description' => $this->t("Example: 'project.firebaseapp.com'"),
      '#default_value' => $config->get('auth_domain'),
      '#required' => TRUE,
    ];

    $form['settings']['database_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database URL'),
      '#description' => $this->t("Example: 'https://project.firebaseio.com'"),
      '#default_value' => $config->get('database_url'),
      '#required' => TRUE,
    ];

    $form['settings']['storage_bucket'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Storage Bucket'),
      '#description' => $this->t("Example: 'project.appspot.com'"),
      '#default_value' => $config->get('storage_bucket'),
      '#required' => TRUE,
    ];

    $form['settings']['messaging_sender_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Messaging sender ID'),
      '#default_value' => $config->get('messaging_sender_id'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('fcm.settings')
      ->set('server_key', trim($form_state->getValue('server_key')))
      ->set('api_key', trim($form_state->getValue('api_key')))
      ->set('project_id', trim($form_state->getValue('project_id')))
      ->set('auth_domain', trim($form_state->getValue('auth_domain')))
      ->set('database_url', trim($form_state->getValue('database_url')))
      ->set('storage_bucket', trim($form_state->getValue('storage_bucket')))
      ->set('messaging_sender_id', trim($form_state->getValue('messaging_sender_id')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
