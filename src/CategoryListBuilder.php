<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a list of template category entities.
 */
class CategoryListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'categories';

  /**
   * @var \Drupal\wysiwyg_template_content\CategoryInterface
   */
  protected $category;

  /**
   * @var \Drupal\wysiwyg_template_content\TemplateContentInterface
   */
  protected $templateForm;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_category_list';
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    $operations['add'] = array(
      'title' => t('Add templates'),
      'weight' => -10,
      'url' => Url::fromRoute('entity.wysiwyg_template_content.add_form', ['wysiwyg_template_category' => $entity->id()]),
    );
    // @todo: If no templates don't show.
    $operations['list'] = [
      'title' => t('List templates'),
      'weight' => $operations['add']['weight'] + 1,
      'url' => $entity->toUrl('overview-form'),
    ];
    if (isset($operations['edit'])) {
      $operations['edit']['title'] = t('Edit category');
    }
    if (isset($operations['delete'])) {
      $operations['delete']['title'] = t('Delete category');
    }

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Category');
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
    // Override the empty text.
    $form[$this->entitiesKey]['#empty'] = $this->t('Add a new category.');
    return $form;
  }

}
