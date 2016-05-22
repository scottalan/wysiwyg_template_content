<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Form\TemplateContentForm.
 */

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class that builds the Template Form.
 *
 * @package Drupal\wysiwyg_template_content\Form
 */
class TemplateContentForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $libraryStorage;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $configTemplateStorage;

  /**
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $templateStorage;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * TemplateContentForm constructor.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->libraryStorage = $entity_type_manager->getStorage('wysiwyg_template_library');
    $this->templateStorage = $entity_type_manager->getStorage('wysiwyg_template_content');
    $this->configTemplateStorage = $entity_type_manager->getStorage('wysiwyg_template');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_add_form';
  }

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
      $form['library'] = [
        '#type' => 'commerce_entity_select',
        '#title' => t('Library'),
        '#target_type' => 'wysiwyg_template_library',
        '#required' => TRUE,
      ];
    }

    // Get the libraries.
//    $libraries = $this->libraryStorage('wysiwyg_template_library')
//      ->loadMultiple();
//    $libraries = array_map(function ($library) {
//      return $library->label();
//    }, $libraries);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#description' => $this->t('Select a name for this template.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#machine_name' => array(
        'exists' => '\Drupal\wysiwyg_template_content\Entity\TemplateContent::load',
      ),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#rows' => 5,
      '#title' => $this->t('Description'),
      '#description' => $this->t('A description to be shown with the template.'),
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
  }

  /**
   * {@inheritdoc}
   */
//  public function save(array $form, FormStateInterface $form_state) {
//    /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface $template */
//    $entity = $this->getEntity();
//
//    $form_state->setRedirect('entity.wysiwyg_template_content.collection');
//    $status = $entity->save();
//
//    switch ($status) {
//      case SAVED_NEW:
//        drupal_set_message($this->t('Created the %label template.', [
//          '%label' => $entity->label(),
//        ]));
//        break;
//
//      default:
//        drupal_set_message($this->t('Saved the %label template.', [
//          '%label' => $entity->label(),
//        ]));
//    }
//  }

}
