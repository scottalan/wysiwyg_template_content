<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Form\TemplateContentForm.
 */

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\node\Entity\NodeType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class that builds the Template Form.
 *
 * @package Drupal\wysiwyg_template_content\Form
 */
class TemplateContentAddForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $templateStorage;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $libraryStorage;

  /**
   * TemplateContentForm constructor.
   *
   * @param EntityManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->templateStorage = $entityTypeManager->getStorage('wysiwyg_template_content');
    $this->libraryStorage = $entityTypeManager->getStorage('wysiwyg_template_library');
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
  public function getFormID() {
    return 'wysiwyg_template_content_add_form';
  }

//  public function form(array $form, FormStateInterface $form_state) {
//    /* @var \Drupal\wysiwyg_template_content\Entity\TemplateContent $wysiwyg_template */
//    $wysiwyg_template = $this->entity;
//    $form =  parent::form($form, $form_state);
//
//    $form['label'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Title'),
//      '#maxlength' => 255,
//      '#default_value' => $wysiwyg_template->label(),
//      '#description' => $this->t('Select a name for this template.'),
//      '#required' => TRUE,
//    ];
//
//    $form['id'] = [
//      '#type' => 'machine_name',
//      '#default_value' => $wysiwyg_template->id(),
//      '#machine_name' => array(
//        'exists' => '\Drupal\wysiwyg_template\Entity\Template::load',
//      ),
//      '#disabled' => !$wysiwyg_template->isNew(),
//    ];
//
//    $form['description'] = [
//      '#type' => 'textfield',
//      '#default_value' => $wysiwyg_template->getDescription(),
//      '#title' => $this->t('Description'),
//      '#description' => $this->t('A description to be shown with the template.'),
//    ];
//
//    $form['body'] = [
//      '#type' => 'text_format',
//      '#format' => $wysiwyg_template->getFormat(),
//      '#default_value' => $wysiwyg_template->getBody(),
//      '#title' => $this->t('HTML template'),
//      '#rows' => 10,
//      '#required' => TRUE,
//    ];
//
//    $node_types = array_map(function ($item) {
//      return $item->label();
//    }, NodeType::loadMultiple());
//
//    $form['node_types'] = [
//      '#type' => 'checkboxes',
//      '#default_value' => $wysiwyg_template->getNodeTypes(),
//      '#title' => $this->t('Available for content types'),
//      '#description' => $this->t('If you select no content type, this template will be available for all content types.'),
//      '#access' => (bool) count($node_types),
//      '#options' => $node_types,
//    ];
//    return $form;
//  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Skip the form if there isn't at least one library.
    $query = $this->libraryStorage->getQuery();
    if ($query->count()->execute() == 0) {
//      $url = Url::fromRoute('entity.wysiwyg_template_library.add_form', )
      $link = Link::createFromRoute('Library', 'entity.wysiwyg_template_library.add_form');
      $form['warning'] = [
        '#markup' => t("Add your first @library now.", ['@library' => $link->toString()]),
      ];
    }
    else {
//      $form['library'] = [
//        '#type' => 'commerce_entity_select',
//        '#title' => t('Library'),
//        '#target_type' => 'wysiwyg_template_library',
//        '#required' => TRUE,
//      ];
    }

    $form['library'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Find a library'),
      '#target_type' => 'wysiwyg_template_library',
      '#selection_settings' => [
        'match_operator' => 'CONTAINS',
      ],
      '#required' => TRUE,
    ];

    $form['template'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Choose a template'),
      '#target_type' => 'wysiwyg_template_content',
      '#selection_settings' => [
        'match_operator' => 'CONTAINS',
      ],
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $template_data = [
      'title' => $values['title'],
      'body' => $values['body'],
      'library_id' => [$values['library_id']],
    ];

    $template = $this->templateStorage->create($template_data);
    $template->save();
    $form_state->setRedirect('entity.wysiwyg_template_content.edit_form', ['wysiwyg_template_content' => $template->id()]);
  }

}
