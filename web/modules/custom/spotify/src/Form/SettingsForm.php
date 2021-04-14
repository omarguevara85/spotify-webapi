<?php

namespace Drupal\spotify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure spotify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['spotify.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['spotify_client_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Client id Spotify'),
      '#default_value' => $this->config('spotify.settings')->get('spotify_client_id'),
      '#required' => TRUE,
    );

    $form['spotify_client_secret'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Client secret Spotify'),
      '#default_value' => $this->config('spotify.settings')->get('spotify_client_secret'),
      '#required' => TRUE,
    );

    $form['spotify_endpoint_token'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint for request authorization'),
      '#maxlength' => 255,
      '#default_value' => $this->config('spotify.settings')->get('spotify_endpoint_token'),
      '#required' => TRUE,
    );

    $form['spotify_endpoint_launch'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint API Get an Launch'),
      '#maxlength' => 255,
      '#default_value' => $this->config('spotify.settings')->get('spotify_endpoint_launch'),
      '#required' => TRUE,
    );

    $form['spotify_endpoint_artist'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint API Get an Artist'),
      '#maxlength' => 255,
      '#default_value' => $this->config('spotify.settings')->get('spotify_endpoint_artist'),
      '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('spotify.settings');

    $config->set('spotify_client_id', $form_state->getValue('spotify_client_id'))->save();
    $config->set('spotify_client_secret', $form_state->getValue('spotify_client_secret'))->save();
    $config->set('spotify_endpoint_token', $form_state->getValue('spotify_endpoint_token'))->save();
    $config->set('spotify_endpoint_launch', $form_state->getValue('spotify_endpoint_launch'))->save();
    $config->set('spotify_endpoint_artist', $form_state->getValue('spotify_endpoint_artist'))->save();
    parent::submitForm($form, $form_state);
  }
}