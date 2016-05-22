<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a wysiwyg template library entity.
 */
interface LibraryInterface extends ConfigEntityInterface {

  /**
   * Returns the library description.
   *
   * @return string
   *   The library description.
   */
  public function getDescription();
}
