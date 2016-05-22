<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a list of template library entities.
 */
class LibraryListBuilder extends ConfigEntityListBuilder{

  /**
   * @var \Drupal\wysiwyg_template_content\LibraryInterface
   */
  protected $libaray;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Library');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity, LibraryInterface $library) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $library->getDescription();
    return $row + parent::buildRow($entity);
  }

}
