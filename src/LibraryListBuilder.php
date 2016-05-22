<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\wysiwyg_template_content\LibraryInterface;

/**
 * Defines a class to build a list of template library entities.
 */
class LibraryListBuilder extends ConfigEntityListBuilder {

  /**
   * @var \Drupal\wysiwyg_template_content\LibraryInterface
   */
  protected $library;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Library');
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
