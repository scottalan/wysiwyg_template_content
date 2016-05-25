<?php

namespace Drupal\wysiwyg_template_content;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\wysiwyg_template_content\TemplateContentInterface;
use Drupal\wysiwyg_template_content\CategoryInterface;
use Drupal\Component\Utility\Html;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateContentListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'templates';

  /**
   * The current template category.
   *
   * @var \Drupal\wysiwyg_template_content\CategoryInterface
   */
  protected $storage;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entity_type;

  /**
   * @var \Drupal\wysiwyg_template_content\TemplateContentInterface
   */
  protected $templateStorage;

  /**
   * Constructs a Category Template Content object.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    $this->entity_type = $entity_type;
    // Set our storage controller.
//    $this->templateStorage = $entity_manager->getStorage('wysiwyg_template_content');
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('entity.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_category_overview';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Template');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    // Set the label here and the parent formats it.
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    // Override the empty text.
    $form[$this->entitiesKey]['#empty'] = $this->t('No templates have been created.');
    return $form;
  }

}
