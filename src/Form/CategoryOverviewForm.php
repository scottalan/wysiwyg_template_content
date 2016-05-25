<?php

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Routing\CurrentRouteMatch;
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
    $values = $this->category->getTemplates();

    // Set the form title to that of the category.
    $title = $this->category->label();

    // The value map allows new values to be added and removed before saving.
    // An array in the $index => $id format. $id is '_new' for unsaved values.
    $value_map = (array) $form_state->get('value_map');
    if (empty($value_map)) {
      $value_map = $values ? array_keys($values) : ['_new'];
      $form_state->set('value_map', $value_map);
    }

    $wrapper_id = Html::getUniqueId('template-categories-category-values-ajax-wrapper');
    $form['values'] = [
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
    if (empty($values)) {
      return $form;
    }

    // Make the weight list always reflect the current number of values.
    // Taken from WidgetBase::formMultipleElements().
    $max_weight = count($value_map);

//    $tree = $this->storageController->loadTree($taxonomy_vocabulary->id(), 0, NULL, TRUE);

    foreach ($value_map as $index => $id) {
      $value_form = &$form['values'][$index];
      // The tabledrag element is always added to the first cell in the row,
      // so we add an empty cell to guide it there, for better styling.
      $value_form['#attributes']['class'][] = 'draggable';
      $value_form['tabledrag'] = [
        '#markup' => '',
      ];

      $value_form['template'] = [
        '#type' => 'link',
        '#title' => t('title here'),
//        '#url' => $term->urlInfo(),
      ];
      if ($id == '_new') {
        $default_weight = $max_weight;
        $remove_access = TRUE;
      }
      else {
        $value = $values[$id];
        $value_form['template']['#default_value'] = $value;
        $default_weight = $value->getWeight();
        $remove_access = $value->access('delete');
      }

      $value_form['weight'] = [
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
        $value_form['#weight'] = $user_input['values'][$index]['weight'];
      }
      else {
        $value_form['#weight'] = $default_weight;
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

//      $value_form['remove'] = [
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
    uasort($form['values'], ['\Drupal\Component\Utility\SortArray', 'sortByWeightProperty']);

    $access_handler = $this->entityTypeManager->getAccessControlHandler('wysiwyg_template_content');
    if ($access_handler->createAccess($this->category->id())) {
      $form['values']['_add_new'] = [
        '#tree' => FALSE,
      ];
      $form['values']['_add_new']['entity'] = [
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
      $form['values']['_add_new']['weight'] = [
        'data' => [],
      ];
      $form['values']['_add_new']['operations'] = [
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
    return $form['values'];
  }

  /**
   * Submit callback for adding a new value.
   */
  public function addValueSubmit(array $form, FormStateInterface $form_state) {
    $value_map = (array) $form_state->get('value_map');
    $value_map[] = '_new';
    $form_state->set('value_map', $value_map);
    $form_state->setRebuild();
  }

  /**
   * Submit callback for removing a value.
   */
  public function removeValueSubmit(array $form, FormStateInterface $form_state) {
    $value_index = $form_state->getTriggeringElement()['#value_index'];
    $value_map = (array) $form_state->get('value_map');
    $value_id = $value_map[$value_index];
    unset($value_map[$value_index]);
    $form_state->set('value_map', $value_map);
    // Non-new values also need to be deleted from storage.
    if ($value_id != '_new') {
      $delete_queue = (array) $form_state->get('delete_queue');
      $delete_queue[] = $value_id;
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
      $value_storage = $this->entityTypeManager->getStorage('wysiwyg_template_content');
      $values = $value_storage->loadMultiple($delete_queue);
      $value_storage->delete($values);
    }

    foreach ($form_state->getValue(['values']) as $index => $value_data) {
      /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface $template */
      $template = $form['values'][$index]['entity']['#entity'];
      $template->setWeight($value_data['weight']);
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
