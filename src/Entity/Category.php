<?php

namespace Drupal\wysiwyg_template_content\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
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
  public function getDescription() {
    return $this->description;
  }

}
