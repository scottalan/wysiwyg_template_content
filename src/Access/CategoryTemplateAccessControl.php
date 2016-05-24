<?php

namespace Drupal\wysiwyg_template_content\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\wysiwyg_template_content\CategoryInterface;

/**
 * Determines access to categories.
 */
class CategoryTemplateAccessControl implements AccessInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a EntityCreateAccessCheck object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Checks access to the template content add page for the category.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   * @param \Drupal\wysiwyg_template_content\CategoryInterface $category
   *   (optional) The node type. If not specified, access is allowed if there
   *   exists at least one node type for which the user may create a node.
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function access(AccountInterface $account, CategoryInterface $category = NULL) {
    $access_control_handler = $this->entityManager->getAccessControlHandler('wysiwyg_template_content');
    // If checking whether a template in a particular category may be created.
    if ($account->hasPermission('administer template categories')) {
      return AccessResult::allowed()->cachePerPermissions();
    }
    if ($category) {
      return $access_control_handler->createAccess($category->id(), $account, [], TRUE);
    }
    // If checking whether a wysiwyg_template_content of any type may be created.
    foreach ($this->entityManager->getStorage('wysiwyg_template_category')->loadMultiple() as $category) {
      if (($access = $access_control_handler->createAccess($category->id(), $account, [], TRUE)) && $access->isAllowed()) {
        return $access;
      }
    }

    // No opinion.
    return AccessResult::neutral();
  }

}
