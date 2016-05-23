<?php

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for category forms.
 */
class CategoryForm extends BundleEntityFormBase {

  /**
   * The category storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $category_storage;

  /**
   * The template storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageBase
   */
  protected $template_storage;

  /**
   * The template storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a new category form.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $category_storage
   *   The category storage.
   */
  public function __construct(ConfigEntityStorageInterface $category_storage, EntityTypeManagerInterface $entity_type_manager) {
    $this->category_storage = $category_storage;
    $this->template_storage = $entity_type_manager->getStorage('wysiwyg_template_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('wysiwyg_template_category'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['#title'] = $this->getQuestion();

    $form['#attributes']['class'][] = 'confirmation';
    $form['description'] = array('#markup' => $this->getDescription());
    $form[$this->getFormName()] = array('#type' => 'hidden', '#value' => 1);

    // By default, render the form using theme_confirm_form().
    if (!isset($form['#theme'])) {
      $form['#theme'] = 'confirm_form';
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\wysiwyg_template_content\CategoryInterface $category */
    $category = $this->entity;
    if ($category->isNew()) {
      $form['#title'] = $this->t('Add category');
    }
    else {
      $form['#title'] = $this->t('Edit category');
    }

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $category->label(),
    );

    $form['category_id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
        'source' => array('name'),
      ),
      '#default_value' => $category->id(),
    );

    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $category->getDescription(),
    );

    $form = parent::form($form, $form_state);
    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    $category = $this->entity;

    $edit_link = $category->toUrl('edit-form')->toString();
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created new category %name.', array('%name' => $category->label())));
        $this->logger('wysiwyg_template_category')->notice('Created new category %name.', array('%name' => $category->label(), 'link' => $edit_link));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('Updated category %name.', array('%name' => $category->label())));
        $this->logger('wysiwyg_template_category')->notice('Updated category %name.', array('%name' => $category->label(), 'link' => $edit_link));
        break;
    }

//    $form_state->setRedirectUrl($category->toUrl('collection'));
    $form_state->setValue('category_id', $category->id());
    $form_state->set('category_id', $category->id());
  }

//  public function buildForm(array $form, FormStateInterface $form_state) {
//    $form = parent::buildForm($form, $form_state);
//    $category = $this->entity;
//    if ($category->isNew()) {
//      $form['#title'] = $this->t('Add category');
//    }
//    else {
//      $form['#title'] = $this->t('Edit category');
//    }
//
//    $form['name'] = array(
//      '#type' => 'textfield',
//      '#title' => $this->t('Name'),
//      '#maxlength' => 255,
//      '#required' => TRUE,
//      '#default_value' => $category->label(),
//    );
//
//    $form['category_id'] = array(
//      '#type' => 'machine_name',
//      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
//      '#machine_name' => array(
//        'exists' => array($this, 'exists'),
//        'source' => array('name'),
//      ),
//      '#default_value' => $category->id(),
//    );
//
//    $form['description'] = array(
//      '#type' => 'textfield',
//      '#title' => $this->t('Description'),
//      '#default_value' => $category->getDescription(),
//    );
//
//    return $this->protectBundleIdElement($form);
//  }

//  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $status = $this->entity->save();
//    $category = $this->entity;
//
//    $edit_link = $category->toUrl('edit-form')->toString();
//    switch ($status) {
//      case SAVED_NEW:
//        drupal_set_message($this->t('Created new category %name.', array('%name' => $category->label())));
//        $this->logger('wysiwyg_template_category')->notice('Created new category %name.', array('%name' => $category->label(), 'link' => $edit_link));
//        break;
//
//      case SAVED_UPDATED:
//        drupal_set_message($this->t('Updated category %name.', array('%name' => $category->label())));
//        $this->logger('wysiwyg_template_category')->notice('Updated category %name.', array('%name' => $category->label(), 'link' => $edit_link));
//        break;
//    }
//
//    $form_state->setRedirectUrl($category->toUrl('collection'));
//    parent::submitForm($form, $form_state);
//  }

  /**
   * Determines if the category already exists.
   *
   * @param string $category_id
   *   The category ID.
   *
   * @return bool
   *   TRUE if the category exists, FALSE otherwise.
   */
  public function exists($category_id) {
    $action = $this->categoryStorage->load($category_id);
    return !empty($action);
  }

}
