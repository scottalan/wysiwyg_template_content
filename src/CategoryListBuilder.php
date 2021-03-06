<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\wysiwyg_template_content\CategoryInterface;

/**
 * Defines a class to build a list of template category entities.
 */
class CategoryListBuilder extends ConfigEntityListBuilder {

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
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    $template = $this->entityType('wysiwyg_template_content');
    $operations['add'] = array(
      'title' => t('Add template'),
      'weight' => 10,
      'url' => $entity->toUrl('add-form', ['wysiwyg_template_category' => $entity->id()]),
    );
    // @todo: If no templates don't show.
    $operations['list'] = [
      'title' => t('Manage templates'),
      'weight' => 0,
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
    $header['id'] = $this->t('Id');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription();
    return $row + parent::buildRow($entity);
  }

}
