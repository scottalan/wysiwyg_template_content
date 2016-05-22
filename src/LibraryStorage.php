<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\wysiwyg_template_content\LibraryStorageInterface;

/**
 * Defines a storage handler class for wysiwyg template libraries.
 */
class LibraryStorage extends ConfigEntityStorage implements LibraryStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function resetCache(array $ids = NULL) {
    drupal_static_reset('wysiwyg_template_library_get_names');
    parent::resetCache($ids);
  }

}
