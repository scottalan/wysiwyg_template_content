<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines a storage handler class for wysiwyg template libraries.
 */
class TemplateContentStorage extends SqlContentEntityStorage implements TemplateContentStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadLibraryTemplates($lid) {

    $tree = array();
    $query = $this->database->select('wysiwyg_template_content_data', 't');
    $result = $query
      ->fields('t')
      ->condition('t.library_id', $lid)
      ->orderBy('t.weight')
      ->orderBy('t.name')
      ->execute();
    foreach ($result as $template) {
      $tree[] = $template;
    }

    return $tree;

  }

}
