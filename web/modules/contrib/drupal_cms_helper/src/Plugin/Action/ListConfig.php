<?php

declare(strict_types=1);

namespace Drupal\drupal_cms_helper\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\eca\Plugin\Action\ConfigurableActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 *   This is an internal part of Drupal CMS and may be changed or removed at any
 *   time without warning. External code should not interact with this class.
 *
 * @todo Remove this class when https://www.drupal.org/i/3558527 is released.
 */
#[Action(
  id: 'eca_config_list',
  label: new TranslatableMarkup('List configuration names'),
)]
final class ListConfig extends ConfigurableActionBase {

  private ConfigFactoryInterface $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->configFactory = $container->get(ConfigFactoryInterface::class);
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'token_name' => 'config_list',
      'prefix' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $configuration = $this->getConfiguration();

    $form['token_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token name'),
      '#description' => $this->t('The name of the token to store the configuration list.'),
      '#default_value' => $configuration['token_name'],
      '#required' => TRUE,
    ];
    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Configuration prefix'),
      '#description' => $this->t('Optional prefix to filter configuration names.'),
      '#default_value' => $configuration['prefix'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->setConfiguration([
      'token_name' => $form_state->getValue('token_name'),
      'prefix' => $form_state->getValue('prefix'),
    ]);
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): void {
    $configuration = $this->getConfiguration();

    $this->tokenService->addTokenData(
      $configuration['token_name'],
      $this->configFactory->listAll($configuration['prefix']),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, ?AccountInterface $account = NULL, $return_as_object = FALSE): AccessResultInterface|bool {
    return $return_as_object ? AccessResult::allowed() : TRUE;
  }

}
