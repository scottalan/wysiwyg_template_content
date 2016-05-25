<?php

namespace Drupal\wysiwyg_template_content\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\node\NodeTypeInterface;
use Drupal\wysiwyg_template_content\CategoryInterface;

/**
 * Defines the WYSIWYG Template Category entity.
 *
 * @ConfigEntityType(
 *   id = "wysiwyg_template_category",
 *   label = @Translation("Category"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\wysiwyg_template_content\Form\CategoryForm",
 *       "delete" = "Drupal\wysiwyg_template_content\Form\CategoryDeleteForm",
 *       "reset" = "Drupal\wysiwyg_template_content\Form\CategoryResetForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\wysiwyg_template_content\CategoryListBuilder",
 *   },
 *   admin_permission = "administer template categories",
 *   config_prefix = "wysiwyg_template_category",
 *   bundle_of = "wysiwyg_template_content",
 *   entity_keys = {
 *     "id" = "category_id",
 *     "label" = "name",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/template-categories/manage/{wysiwyg_template_category}/add",
 *     "edit-form" = "/admin/structure/template-categories/manage/{wysiwyg_template_category}",
 *     "delete-form" = "/admin/structure/template-categories/manage/{wysiwyg_template_category}/delete",
 *     "overview-form" = "/admin/structure/template-categories/manage/{wysiwyg_template_category}/overview",
 *     "collection" = "/admin/structure/template-categories",
 *     "reset-form" = "/admin/structure/template-categories/manage/{wysiwyg_template_category}/reset",
 *   },
 *   config_export = {
 *     "name",
 *     "category_id",
 *     "description",
 *     "weight",
 *   }
 * )
 */
class Category extends ConfigEntityBundleBase implements CategoryInterface {

  /**
   * The category ID.
   *
   * @var string
   */
  protected $category_id;

  /**
   * Name of the category.
   *
   * @var string
   */
  protected $name;

  /**
   * Description of the category.
   *
   * @var string
   */
  protected $description;

  /**
   * The weight of this category in relation to other categories.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * The node types the category will show on.
   *
   * @var string[]
   */
  protected $node_types;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->category_id;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplates() {
    $storage = $this->entityTypeManager()->getStorage('wysiwyg_template_content');
    return $storage->loadByProperties(['category_id' => $this->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeTypes() {
    return $this->node_types ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    $this->node_types = array_values(array_filter($this->getNodeTypes()));
    parent::save();
  }

  /**
   * {@inheritdoc}
   */
  public static function loadByNodeType(NodeTypeInterface $node_type = NULL) {
    /** @var \Drupal\wysiwyg_template_content\CategoryInterface[] $categories */
    $categories = static::loadMultiple();
    foreach ($categories as $id => $category) {
      if (!$node_type) {
        // If no node type is passed than all templates that *don't specify any*
        // types are included, but those specifying a type are not.
        if (!empty($category->getNodeTypes())) {
          unset($categories[$id]);
        }
      }
      else {
        // Any templates without types, plus the templates that specify this type.
        if (empty($category->getNodeTypes()) || in_array($node_type->id(), $category->getNodeTypes())) {
          continue;
        }
        unset($categories[$id]);
      }
    }

    return $categories;
  }

}
