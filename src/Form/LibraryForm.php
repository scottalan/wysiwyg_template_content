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
 * Base form for vocabulary edit forms.
 */
class LibraryForm extends BundleEntityFormBase {

  /**
   * The library storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $library_storage;

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
   * Constructs a new library form.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $library_storage
   *   The library storage.
   */
  public function __construct(ConfigEntityStorageInterface $library_storage, EntityTypeManagerInterface $entity_type_manager) {
    $this->library_storage = $library_storage;
    $this->template_storage = $entity_type_manager->getStorage('wysiwyg_template_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('wysiwyg_template_library'),
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
    /** @var \Drupal\wysiwyg_template_content\LibraryInterface $library */
    $library = $this->entity;
    if ($library->isNew()) {
      $form['#title'] = $this->t('Add library');
    }
    else {
      $form['#title'] = $this->t('Edit library');
    }

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $library->label(),
    );

    $form['library_id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
        'source' => array('name'),
      ),
      '#default_value' => $library->id(),
    );

    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $library->getDescription(),
    );

    $form = parent::form($form, $form_state);
    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    $library = $this->entity;

    $edit_link = $library->toUrl('edit-form')->toString();
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created new library %name.', array('%name' => $library->label())));
        $this->logger('wysiwyg_template_library')->notice('Created new library %name.', array('%name' => $library->label(), 'link' => $edit_link));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('Updated library %name.', array('%name' => $library->label())));
        $this->logger('wysiwyg_template_library')->notice('Updated library %name.', array('%name' => $library->label(), 'link' => $edit_link));
        break;
    }

//    $form_state->setRedirectUrl($library->toUrl('collection'));
    $form_state->setValue('library_id', $library->id());
    $form_state->set('library_id', $library->id());
  }

//  public function buildForm(array $form, FormStateInterface $form_state) {
//    $form = parent::buildForm($form, $form_state);
//    $library = $this->entity;
//    if ($library->isNew()) {
//      $form['#title'] = $this->t('Add library');
//    }
//    else {
//      $form['#title'] = $this->t('Edit library');
//    }
//
//    $form['name'] = array(
//      '#type' => 'textfield',
//      '#title' => $this->t('Name'),
//      '#maxlength' => 255,
//      '#required' => TRUE,
//      '#default_value' => $library->label(),
//    );
//
//    $form['library_id'] = array(
//      '#type' => 'machine_name',
//      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
//      '#machine_name' => array(
//        'exists' => array($this, 'exists'),
//        'source' => array('name'),
//      ),
//      '#default_value' => $library->id(),
//    );
//
//    $form['description'] = array(
//      '#type' => 'textfield',
//      '#title' => $this->t('Description'),
//      '#default_value' => $library->getDescription(),
//    );
//
//    return $this->protectBundleIdElement($form);
//  }

//  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $status = $this->entity->save();
//    $library = $this->entity;
//
//    $edit_link = $library->toUrl('edit-form')->toString();
//    switch ($status) {
//      case SAVED_NEW:
//        drupal_set_message($this->t('Created new library %name.', array('%name' => $library->label())));
//        $this->logger('wysiwyg_template_library')->notice('Created new library %name.', array('%name' => $library->label(), 'link' => $edit_link));
//        break;
//
//      case SAVED_UPDATED:
//        drupal_set_message($this->t('Updated library %name.', array('%name' => $library->label())));
//        $this->logger('wysiwyg_template_library')->notice('Updated library %name.', array('%name' => $library->label(), 'link' => $edit_link));
//        break;
//    }
//
//    $form_state->setRedirectUrl($library->toUrl('collection'));
//    parent::submitForm($form, $form_state);
//  }

  /**
   * Determines if the library already exists.
   *
   * @param string $library_id
   *   The library ID.
   *
   * @return bool
   *   TRUE if the library exists, FALSE otherwise.
   */
  public function exists($library_id) {
    $action = $this->libraryStorage->load($library_id);
    return !empty($action);
  }

}
