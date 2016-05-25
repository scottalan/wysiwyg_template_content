<?php

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\user\Plugin\views\filter\Name;
use Drupal\Core\Entity\Element\EntityAutocomplete;
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
  protected $storage;

  /**
   * The template storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageBase
   */
  protected $template_storage;

  /**
   * Constructor for the category form.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $storage
   *   The category storage.
   */
  public function __construct(ConfigEntityStorageInterface $storage, EntityTypeManagerInterface $entity_type_manager) {
    $this->storage = $storage;
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
        drupal_set_message($this->t('Created a new category: %name.', array('%name' => $category->label())));
        $this->logger('wysiwyg_template_category')->notice('Created new category %name.', array('%name' => $category->label(), 'link' => $edit_link));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('Updated category: %name.', array('%name' => $category->label())));
        $this->logger('wysiwyg_template_category')->notice('Updated category %name.', array('%name' => $category->label(), 'link' => $edit_link));
        break;
    }

    $form_state->setRedirectUrl($category->toUrl('collection'));
  }

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
    $action = $this->category_storage->load($category_id);
    return !empty($action);
  }

}
