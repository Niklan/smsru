<?php

/**
 * @file
 *   Rules integration hooks.
 */

/**
 * Implements hook_rules_action_info().
 */
function smsru_rules_action_info() {
  return array(
    'smsru_send_sms' => array(
      'label' => t('Send SMS'),
      'group' => 'SMS.RU',
      'parameter' => array(
        'to' => array(
          'type' => 'text',
          'label' => t('Telephone'),
        ),
        'text' => array(
          'type' => 'text_formatted',
          'label' => t('Message'),
        ),
        'from' => array(
          'type' => 'text',
          'label' => t('From'),
          'optional' => TRUE,
          'default value' => variable_get('smsru_default_from', ''),
        ),
      ),
      'callbacks' => array(
        'execute' => 'smsru_rules_action_send_sms',
      ),
    ),
  );
}

/**
 * Action callback.
 */
function smsru_rules_action_send_sms($to, $text, $from) {
  smsru_class()->send_sms($to, check_markup($text['value'], $text['format']), array(
    'from' => $from,
  ));
}
