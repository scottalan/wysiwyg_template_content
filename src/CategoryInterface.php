<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a wysiwyg template category entity.
 */
interface CategoryInterface extends ConfigEntityInterface {

  /**
   * Get the templates in this category.
   *
   * @return \Drupal\wysiwyg_template_content\CategoryInterface[]
   */
  public function getTemplates();

  /**
   * Returns the category description.
   *
   * @return string
   *   The category description.
   */
  public function getDescription();
}
