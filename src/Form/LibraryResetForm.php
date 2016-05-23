<?php

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the confirmation form for resetting template ordering.
 */
class LibraryResetForm extends EntityConfirmFormBase {

  /**
   * The template storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageBase
   */
  protected $templateStorage;

  /**
   * Constructs a new LibraryResetForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The attribute storage.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->templateStorage = $entity_type_manager->getStorage('wysiwyg_template_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_content_confirm_reset_alphabetical';
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entity->toUrl('overview-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to reset the @library template values to alphabetical order?', ['@library' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the new weights from an ordered query.
    $query = $this->templateStorage->getQuery();
    $query
      ->condition('attribute', $this->entity->id())
      ->sort('name');
    $template_ids = $query->execute();
    /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface[] $templates */
    $templates = $this->templateStorage->loadMultiple($template_ids);
    $new_weight = 0;
    foreach ($templates as $template) {
      $template->setWeight($new_weight);
      $template->save();
      $new_weight++;
    }

    drupal_set_message($this->t('The @template template values have been reset to alphabetical order.', ['@template' => $this->entity->label()]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
