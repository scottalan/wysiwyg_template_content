<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\TemplateContentListBuilder.
 */

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a listing of TemplateContent entities.
 */
class TemplateContentListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_content_list_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Template Content');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Better empty text.
    $form[$this->entitiesKey]['#empty'] = $this->t('There are no WYSIWYG templates yet.');

    return $form;
  }

}
