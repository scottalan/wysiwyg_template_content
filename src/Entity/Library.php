<?php

namespace Drupal\wysiwyg_template_content\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\wysiwyg_template_content\LibraryInterface;

/**
 * Defines the taxonomy vocabulary entity.
 *
 * @ConfigEntityType(
 *   id = "wysiwyg_template_library",
 *   label = @Translation("Library"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\wysiwyg_template_content\Form\LibraryForm",
 *       "edit" = "Drupal\wysiwyg_template_content\Form\LibraryForm",
 *       "delete" = "Drupal\wysiwyg_template_content\Form\LibraryDeleteForm",
 *       "reset" = "Drupal\wysiwyg_template_content\Form\LibraryResetForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\wysiwyg_template_content\LibraryListBuilder",
 *   },
 *   admin_permission = "administer template libraries",
 *   config_prefix = "wysiwyg_template_library",
 *   bundle_of = "wysiwyg_template_content",
 *   entity_keys = {
 *     "id" = "library_id",
 *     "label" = "name",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "add-form" = "/admin/wysiwyg-template/libraries/manage/{wysiwyg_template_library}/add",
 *     "edit-form" = "/admin/wysiwyg-template/libraries/manage/{wysiwyg_template_library}",
 *     "delete-form" = "/admin/wysiwyg-template/libraries/manage/{wysiwyg_template_library}/delete",
 *     "overview-form" = "/admin/wysiwyg-template/libraries/manage/{wysiwyg_template_library}/overview",
 *     "collection" = "/admin/wysiwyg-template/libraries",
 *     "reset-form" = "/admin/wysiwyg-template/libraries/manage/{wysiwyg_template_library}/reset",
 *   },
 *   config_export = {
 *     "name",
 *     "library_id",
 *     "description",
 *     "weight",
 *   }
 * )
 */
class Library extends ConfigEntityBundleBase implements LibraryInterface {

  /**
   * The library ID.
   *
   * @var string
   */
  protected $library_id;

  /**
   * Name of the library.
   *
   * @var string
   */
  protected $name;

  /**
   * Description of the library.
   *
   * @var string
   */
  protected $description;

  /**
   * The weight of this library in relation to other libraries.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->library_id;
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
  public function getValues() {
    $storage = $this->entityTypeManager()->getStorage('wysiwyg_template_content');
    return $storage->loadByProperties(['library_id' => $this->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

}
