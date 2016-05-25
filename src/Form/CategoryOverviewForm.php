<?php

namespace Drupal\wysiwyg_template_content\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Utility\Html;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryOverviewForm extends FormBase {

  /**
   * The current template category.
   *
   * @var \Drupal\wysiwyg_template_content\CategoryInterface
   */
  protected $category;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  protected $templateStorage;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_category_overview';
  }

  /**
   * Constructs a new CategoryOverviewForm object.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(CurrentRouteMatch $current_route_match, EntityTypeManagerInterface $entity_type_manager) {
    $this->category = $current_route_match->getParameter('wysiwyg_template_category');
    $this->templateStorage = $entity_type_manager->getStorage('wysiwyg_template_content');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user_input = $form_state->getUserInput();
    $templates = $this->category->getTemplates();

    // Set the form title to that of the category.
    $title = $this->category->label();

    // The value map allows new values to be added and removed before saving.
    // An array in the $index => $id format. $id is '_new' for unsaved values.
    $template_map = (array) $form_state->get('value_map');
    if (empty($template_map)) {
      $template_map = $templates ? array_keys($templates) : ['_new'];
      $form_state->set('value_map', $template_map);
    }

    $wrapper_id = Html::getUniqueId('template-categories-category-values-ajax-wrapper');
    $form['templates'] = [
      '#type' => 'table',
      '#header' => [
        ['data' => $this->t('Template'), 'colspan' => 2],
        $this->t('Weight'),
        $this->t('Operations'),
      ],
      '#empty' => $this->t('No templates available. <a href=":link">Add a template to :title</a>.', array(':title' => $title, ':link' => $this->url('entity.wysiwyg_template_content.add_form', array('wysiwyg_template_category' => $this->category->id())))),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'template-categories-category-value-order-weight',
        ],
      ],
      '#weight' => 5,
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
      // #input defaults to TRUE, which breaks file fields in the IEF element.
      // This table is used for visual grouping only, the element itself
      // doesn't have any values of its own that need processing.
      '#input' => FALSE,
    ];

    // If there are not templates just return the form now.
    if (empty($templates)) {
      return $form;
    }

    // Make the weight list always reflect the current number of values.
    // Taken from WidgetBase::formMultipleElements().
    $max_weight = count($template_map);

//    $tree = $this->storageController->loadTree($taxonomy_vocabulary->id(), 0, NULL, TRUE);

    foreach ($template_map as $index => $id) {
      $template_form = &$form['templates'][$index];
      // The tabledrag element is always added to the first cell in the row,
      // so we add an empty cell to guide it there, for better styling.
      $template_form['#attributes']['class'][] = 'draggable';
      $template_form['tabledrag'] = [
        '#markup' => '',
      ];

      $template_form['template'] = [
        '#type' => 'link',
        '#title' => t('title here'),
//        '#url' => $term->urlInfo(),
      ];
      if ($id == '_new') {
        $default_weight = $max_weight;
        $remove_access = TRUE;
      }
      else {
        $value = $templates[$id];
        $template_form['template']['#default_value'] = $value;
        $default_weight = $value->getWeight();
        $remove_access = $value->access('delete');
      }

      $template_form['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#delta' => $max_weight,
        '#default_value' => $default_weight,
        '#attributes' => [
          'class' => ['template-categories-category-value-order-weight'],
        ],
      ];
      // Used by SortArray::sortByWeightProperty to sort the rows.
      if (isset($user_input['values'][$index])) {
        $template_form['#weight'] = $user_input['values'][$index]['weight'];
      }
      else {
        $template_form['#weight'] = $default_weight;
      }


      $destination = $this->getDestinationArray();

      $operations = [
        'remove' => [
          '#type' => 'submit',
          '#name' => 'remove_value' . $index,
          '#value' => $this->t('Remove'),
          '#limit_validation_errors' => [],
          '#submit' => ['::removeValueSubmit'],
          '#value_index' => $index,
          '#ajax' => [
            'callback' => '::valuesAjax',
            'wrapper' => $wrapper_id,
          ],
          '#access' => $remove_access,
        ],
        'delete' => [
          'title' => $this->t('Delete'),
          'query' => $destination,
          'url' => $template->urlInfo('delete-form'),
        ],
      ];

//      $template_form['remove'] = [
//        '#type' => 'submit',
//        '#name' => 'remove_value' . $index,
//        '#value' => $this->t('Remove'),
//        '#limit_validation_errors' => [],
//        '#submit' => ['::removeValueSubmit'],
//        '#value_index' => $index,
//        '#ajax' => [
//          'callback' => '::valuesAjax',
//          'wrapper' => $wrapper_id,
//        ],
//        '#access' => $remove_access,
//      ];
    }

    // Sort the values by weight. Ensures weight is preserved on ajax refresh.
    uasort($form['templates'], ['\Drupal\Component\Utility\SortArray', 'sortByWeightProperty']);

    $access_handler = $this->entityTypeManager->getAccessControlHandler('wysiwyg_template_content');
    if ($access_handler->createAccess($this->category->id())) {
      $form['templates']['_add_new'] = [
        '#tree' => FALSE,
      ];
      $form['templates']['_add_new']['entity'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add'),
        '#submit' => ['::addValueSubmit'],
        '#limit_validation_errors' => [],
        '#ajax' => [
          'callback' => '::valuesAjax',
          'wrapper' => $wrapper_id,
        ],
        '#prefix' => '<div class="template-categories-category-value-new">',
        '#suffix' => '</div>',
      ];
      $form['templates']['_add_new']['weight'] = [
        'data' => [],
      ];
      $form['templates']['_add_new']['operations'] = [
        'data' => [],
      ];
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save templates'),
      '#button_type' => 'primary',
    ];
    $form['actions']['reset_alphabetical'] = [
      '#type' => 'submit',
      '#submit' => ['::submitReset'],
      '#value' => $this->t('Reset to alphabetical'),
    ];

    return $form;
  }

  /**
   * Ajax callback for value operations.
   */
  public function valuesAjax(array $form, FormStateInterface $form_state) {
    return $form['templates'];
  }

  /**
   * Submit callback for adding a new value.
   */
  public function addValueSubmit(array $form, FormStateInterface $form_state) {
    $template_map = (array) $form_state->get('value_map');
    $template_map[] = '_new';
    $form_state->set('value_map', $template_map);
    $form_state->setRebuild();
  }

  /**
   * Submit callback for removing a value.
   */
  public function removeValueSubmit(array $form, FormStateInterface $form_state) {
    $template_index = $form_state->getTriggeringElement()['#value_index'];
    $template_map = (array) $form_state->get('value_map');
    $template_id = $template_map[$template_index];
    unset($template_map[$template_index]);
    $form_state->set('value_map', $template_map);
    // Non-new values also need to be deleted from storage.
    if ($template_id != '_new') {
      $delete_queue = (array) $form_state->get('delete_queue');
      $delete_queue[] = $template_id;
      $form_state->set('delete_queue', $delete_queue);
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $delete_queue = $form_state->get('delete_queue');
    if (!empty($delete_queue)) {
      $template_storage = $this->entityTypeManager->getStorage('wysiwyg_template_content');
      $templates = $template_storage->loadMultiple($delete_queue);
      $template_storage->delete($templates);
    }

    foreach ($form_state->getValue(['values']) as $index => $template_data) {
      /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface $template */
      $template = $form['templates'][$index]['entity']['#entity'];
      $template->setWeight($template_data['weight']);
      $template->save();
    }

    drupal_set_message($this->t('Saved the @category category values.', ['@category' => $this->category->label()]));
  }

  /**
   * Redirects to the confirmation form for the reset action.
   */
  public function submitReset(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl($this->category->toUrl('reset-form'));
  }

}
