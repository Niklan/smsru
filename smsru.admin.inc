<?php

/**
 * @file
 *   Administrative interface callbacks and forms.
 */

/**
 * Settings page for module.
 */
function smsru_settings_page($form, &$form_state) {
  $form['smsru_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('API Key'),
    '#required' => TRUE,
    '#default_value' => _smsru_get_api_key(),
  );

  $form['smsru_default_from'] = array(
    '#type' => 'textfield',
    '#title' => t('Default "from" value'),
    '#description' => 'Default value for parameter "from" if available and not passed as argument.',
    '#required' => FALSE,
    '#default_value' => variable_get('smsru_default_from', ''),
  );

  return system_settings_form($form);
}