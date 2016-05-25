<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateListBuilder extends FormBase {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'templates';

  /**
   * {@inheritdoc}
   */
  protected $entities = array();

  /**
   * The title.
   */
  protected $title;

  /**
   * The current template category.
   *
   * @var \Drupal\wysiwyg_template_content\CategoryInterface
   */
  protected $category;

  /**
   * The current template category.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $categoryStorage;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * @var \Drupal\wysiwyg_template_content\TemplateContentInterface
   */
  protected $templateStorage;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    // @todo: Create a listing of templates under categories.
//    /** @var \Drupal\wysiwyg_template_content\Entity\Category $wysiwyg_template_category */
    $this->templateStorage = $entityTypeManager->getStorage('wysiwyg_template_content');
    $this->categoryStorage = $entityTypeManager->getStorage('wysiwyg_template_category');
//    $this->title = $this->t('@label templates', ['@label' => $wysiwyg_template_category->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_category_overview';
  }

  public function getOperations(EntityInterface $entity) {

  }

  public function buildOperations(EntityTypeInterface $entity) {
    $build = array(
      '#type' => 'operations',
      '#links' => $this->getOperations($entity),
    );

    return $build;
  }
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Template');
    $header['operations'] = $this->t('Operations');
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = array();
    if (!empty($this->weightKey)) {
      // Override default values to markup elements.
      $row['#attributes']['class'][] = 'draggable';
      $row['#weight'] = $entity->get($this->weightKey);
      // Add weight column.
      $row['weight'] = array(
        '#type' => 'weight',
        '#title' => t('Weight for @title', array('@title' => $entity->label())),
        '#title_display' => 'invisible',
        '#default_value' => $entity->get($this->weightKey),
        '#attributes' => array('class' => array('weight')),
      );
      $row['operations']['data'] = $this->buildOperations($entity);
      return $row;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $templates = $this->entities;
    // Templates are stored beginning with 1.
    $index = 0;
    do {
      // If there are no templates break.
      if (empty($templates)) {
        break;
      }
      // @todo:
    } while (isset($templates[++$index]));

    $url = Url::fromRoute('entity.wysiwyg_template_content.add_form', ['wysiwyg_template_category' => $this->category->id()]);

    // Build the form.
    $form[$this->entitiesKey] = array(
      '#type' => 'table',
      '#header' => array($this->t('Template'), $this->t('Weight'), $this->t('Operations')),
      '#empty' => $this->t('Create a template. <a href=":link">Add template</a>.', array(':link' => $url)),
      '#attributes' => array(
        'id' => $this->getFormId(),
      ),
    );

    foreach ($templates as $id => $template) {
      $form[$this->entitiesKey][$id]['#item'] = $template;

      // @todo: Left off trying to get a listing of each of the templates.
    }
    // Override the empty text.
    $form[$this->entitiesKey]['#empty'] = $this->t('No templates have been created.');
    return $form;
  }

  public function render() {
    $build['table'] = array(
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#title' => $this->title,
      '#rows' => array(),
      '#empty' => $this->t('There is no @label yet.', array('@label' => $this->entityType->getLabel())),
      '#cache' => [
        'contexts' => $this->entityType->getListCacheContexts(),
        'tags' => $this->entityType->getListCacheTags(),
      ],
    );
    foreach ($this->load() as $entity) {
      if ($row = $this->buildRow($entity)) {
        $build['table']['#rows'][$entity->id()] = $row;
      }
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $build['pager'] = array(
        '#type' => 'pager',
      );
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValue($this->entitiesKey) as $id => $value) {
      if (isset($this->entities[$id]) && $this->entities[$id]->get($this->weightKey) != $value['weight']) {
        // Save entity only when its weight was changed.
        $this->entities[$id]->set($this->weightKey, $value['weight']);
        $this->entities[$id]->save();
      }
    }
  }

}
