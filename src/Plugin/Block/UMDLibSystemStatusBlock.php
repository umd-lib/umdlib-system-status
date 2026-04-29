<?php

namespace Drupal\umdlib_system_status\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a UMDLib System Status block.
 *
 * @Block(
 *   id = "umdlib_system_status_block",
 *   admin_label = @Translation("UMDLib System Status")
 * )
 */
class UMDLibSystemStatusBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'umdlib_system_status',
      '#content' => $this->configuration['content'] ?? '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['content'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Content'),
      '#description' => $this->t('Enter content to display in the system status block.'),
      '#default_value' => $this->configuration['content'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['content'] = $form_state->getValue('content');
  }

}
