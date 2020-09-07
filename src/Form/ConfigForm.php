<?php

namespace Drupal\css_aspect_ratio\Form;

use Drupal\breakpoint\BreakpointManager;
use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 */
class ConfigForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'css_aspect_ratio_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('css_aspect_ratio.config');
    $breakpoints = $this->getBreakpointGroups();
    $form['breakpoints'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Breakpoints to use'),
      '#description' => $this->t(''),
      '#default_value' => $config->get('breakpoints'),
      '#weight' => '0',
      '#options' => $breakpoints
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function getBreakpointGroups() {
    $breakpoint_manager = \Drupal::service('breakpoint.manager');
    $breakpoints = $breakpoint_manager->getGroups();
    return $breakpoints;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('css_aspect_ratio.config')
      ->set('breakpoints', $form_state->getValue('breakpoints'))
      ->save();
    $this->messenger()->addMessage('Css Aspect Ratio settings stored successfully.');
  }

}
