<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a wysiwyg template library entity.
 */
interface LibraryInterface extends ConfigEntityInterface {

  /**
   * Gets the library values.
   *
   * @return \Drupal\wysiwyg_template_content\LibraryInterface[]
   */
  public function getValues();

  /**
   * Returns the library description.
   *
   * @return string
   *   The library description.
   */
  public function getDescription();
}
