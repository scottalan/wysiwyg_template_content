<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\TemplateContentInterface.
 */

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\node\NodeTypeInterface;

/**
 * Provides an interface for defining a template entity.
 */
interface TemplateContentInterface extends ContentEntityInterface {


  /**
   * Sets the template description.
   *
   * @param string $description
   *   The template description.
   *
   * @return $this
   */
  public function setDescription($description);

  /**
   * Gets the template description.
   *
   * @return string
   *   The template description.
   */
  public function getDescription();

  /**
   * Gets the template body.
   *
   * @return string
   *   The template HTML body.
   */
  public function getBody();

  /**
   * Gets the template body format.
   *
   * @return string
   *   The template HTML body format.
   */
//  public function getBodyFormat();

  /**
   * Sets the template body format.
   *
   * @return $this
   */
//  public function setBodyFormat($format);

  /**
   * Sets the text format name for the template description.
   *
   * @param string $format
   *   The template description text format.
   *
   * @return $this
   */
  public function setFormat($format);

  /**
   * Gets the template text format.
   *
   * @return string
   *   The text format for the template body.
   */
  public function getFormat();

  /**
   * Sets the name of the template.
   *
   * @param int $name
   *   The template name.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the name of the template.
   *
   * @return string
   *   The name of the template.
   */
  public function getName();

  /**
   * Gets the weight of this template.
   *
   * @param int $weight
   *   The template weight.
   *
   * @return $this
   */
  public function setWeight($weight);

  /**
   * Gets the template weight.
   *
   * @return int
   *   The template weight.
   */
  public function getWeight();

  /**
   * Get the category id this template belongs to.
   *
   * @return int
   *   The id of the category.
   */
  public function getCategoryId();

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
