<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines a storage handler class for wysiwyg template categories.
 */
class TemplateContentStorage extends SqlContentEntityStorage implements TemplateContentStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadCategoryTemplates($lid) {

    $tree = array();
    $query = $this->database->select('wysiwyg_template_content_data', 't');
    $result = $query
      ->fields('t')
      ->condition('t.category_id', $lid)
      ->orderBy('t.weight')
      ->orderBy('t.name')
      ->execute();
    foreach ($result as $template) {
      $tree[] = $template;
    }

    return $tree;

  }

}
