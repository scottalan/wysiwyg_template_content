<?php

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Url;
use Drupal\wysiwyg_template_content\LibraryInterface;
use Drupal\wysiwyg_template_content\TemplateContentStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a templates listing form for a libraries.
 */
class LibraryTemplates extends FormBase {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The template storage handler.
   *
   * @var \Drupal\wysiwyg_template_content\TemplateContentStorage
   */
  protected $storageController;

  /**
   * Constructs a Library Template Content object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(ModuleHandlerInterface $module_handler, EntityManagerInterface $entity_manager) {
    $this->moduleHandler = $module_handler;
    // Set our storage controller.
    $this->storageController = $entity_manager->getStorage('wysiwyg_template_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_content_library_templates';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, LibraryInterface $library = NULL) {
    $form_state->set(['wysiwyg_template_content', 'wysiwyg_template_library'], $library);

    $entries = 0;

    // An array of the terms to be displayed on this page.
    $current_page = array();

    $delta = 0;
    $template_deltas = array();
    $tree = $this->storageController->loadLibraryTemplates($library->id());
    $index = 0;
    do {
      // In case this tree is completely empty.
      if (empty($tree)) {
        break;
      }
      $entries++;
      $delta++;

      $template = $tree[$index];
      $template_deltas[$template->id()] = isset($template_deltas[$template->id()]) ? $template_deltas[$template->id()] + 1 : 0;
      $key = 'template_id:' . $template->id() . ':' . $template_deltas[$template->id()];

      // Keep track of the first term displayed on this page.
      if ($entries == 1) {
        $form['#first_tid'] = $template->id();
      }
      $current_page[$key] = $template;
    } while (isset($tree[++$index]));

    // If this form was already submitted once, it's probably hit a validation
    // error. Ensure the form is rebuilt in the same order as the user
    // submitted.
    $user_input = $form_state->getUserInput();
    if (!empty($user_input)) {
      // Get the POST order.
      $order = array_flip(array_keys($user_input['templates']));
      // Update our form with the new order.
      $current_page = array_merge($order, $current_page);
    }

    $errors = $form_state->getErrors();
    $destination = $this->getDestinationArray();

    // Add an error class if this row contains a form error.
    foreach ($errors as $error_key => $error) {
      if (strpos($error_key, $key) === 0) {
        $form['templates'][$key]['#attributes']['class'][] = 'error';
      }
    }

    // Build the actual form.
    $form['templates'] = array(
      '#type' => 'table',
      '#header' => array($this->t('Name'), $this->t('Weight'), $this->t('Operations')),
      '#empty' => $this->t('No templates available. <a href=":link">Add template</a>.', array(':link' => Url::fromRoute('wysiwyg_template_content.add', array('wysiwyg_template_library' => $library->id())))),
      '#attributes' => array(
        'id' => 'wysiwyg_template_content',
      ),
    );
    foreach ($current_page as $key => $template) {
      /** @var $template \Drupal\Core\Entity\EntityInterface */
      $form['templates'][$key]['#template'] = $template;

      $form['templates'][$key]['template'] = array(
        '#type' => 'link',
        '#title' => $template->getName(),
        '#url' => $template->toUrl('collection'),
      );

      $form['templates'][$key]['weight'] = array(
        '#type' => 'weight',
        '#delta' => $delta,
        '#title' => $this->t('Weight for added template'),
        '#title_display' => 'invisible',
        '#default_value' => $template->getWeight(),
        '#attributes' => array(
          'class' => array('template-weight'),
        ),
      );

      $operations = array(
        'edit' => array(
          'title' => $this->t('Edit'),
          'query' => $destination,
          'url' => $template->toUrl('edit-form'),
        ),
        'delete' => array(
          'title' => $this->t('Delete'),
          'query' => $destination,
          'url' => $template->toUrl('delete-form'),
        ),
      );

      $form['templates'][$key]['operations'] = array(
        '#type' => 'operations',
        '#links' => $operations,
      );

      $form['templates'][$key]['#attributes']['class'] = array();

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
