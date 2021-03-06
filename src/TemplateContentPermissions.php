<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions for the wysiwyg_template_content module.
 *
 * @see wysiwyg_template_content.permissions.yml
 */
class TemplateContentPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a TemplateContentPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'));
  }

  /**
   * Get template permissions.
   *
   * @return array
   *   Permissions array.
   */
  public function permissions() {
    $permissions = [];
    foreach ($this->entityManager->getStorage('wysiwyg_template_category')->loadMultiple() as $category) {
      $permissions += [
        'edit templates in ' . $category->id() => [
          'title' => $this->t('Edit templates in %category', ['%category' => $category->label()]),
        ],
      ];
      $permissions += [
        'delete templates in ' . $category->id() => [
          'title' => $this->t('Delete templates from %category', ['%category' => $category->label()]),
        ],
      ];
    }
    return $permissions;
  }

}
