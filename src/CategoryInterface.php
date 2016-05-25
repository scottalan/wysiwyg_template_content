<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\node\NodeTypeInterface;

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

  /**
   * Gets the list of allowed node types.
   *
   * @return string[]
   */
  public function getNodeTypes();

  /**
   * Loads templates filtered by node type.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   (optional) The node type to filter by. If this is not passed, only
   *   templates that specify *no* types will be returned.
   * @return \Drupal\wysiwyg_template_content\TemplateContentInterface[]
   *   The list of available templates filtered by node type.
   */
  public static function loadByNodeType(NodeTypeInterface $node_type = NULL);

}
