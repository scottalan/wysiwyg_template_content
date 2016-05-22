<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Controller\WysiwygTemplateContentController.
 */

namespace Drupal\wysiwyg_template_content\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\wysiwyg_template_content\TemplateContentInterface;
use Drupal\wysiwyg_template_content\LibraryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Default controller for the wysiwyg_template_content module.
 */
class WysiwygTemplateContentController extends ControllerBase {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  public $entityManager;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  public $formBuilder;

  /**
   * @var \Drupal\wysiwyg_template\Form\TemplateForm
   */
  public $templateForm;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  public $storage;

  /**
   * Returns a form to add a new template to a library.
   *
   * @param \Drupal\wysiwyg_template_content\LibraryInterface $wysiwyg_template_library
   *   The library the template will be added to.
   *
   * @return array
   *   The template add form.
   */
  public function addForm(LibraryInterface $wysiwyg_template_library) {
    if ($wysiwyg_template_library) {
      $template = $this->entityManager->getStorage('wysiwyg_template_content')->create(array('library_id' => $wysiwyg_template_library->id()));
      return $this->formBuilder->getForm($template);
    }
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\wysiwyg_template_content\LibraryInterface $library
   *   The vocabulary.
   *
   * @return string
   *   The vocabulary label as a render array.
   */
  public function libraryTitle(LibraryInterface $library) {
    return ['#markup' => $library->label(), '#allowed_tags' => Xss::getHtmlTagList()];
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\wysiwyg_template_content\TemplateContentInterface $template
   *   The taxonomy term.
   *
   * @return array
   *   The term label as a render array.
   */
  public function templateTitle(TemplateContentInterface $template) {
    return ['#markup' => $template->getName(), '#allowed_tags' => Xss::getHtmlTagList()];
  }

  /**
   * The _title_callback for the wysiwyg_template_content.add route.
   *
   * @param \Drupal\wysiwyg_template_content\LibraryInterface $library
   *   The current template.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(LibraryInterface $library) {
    return $this->t('Create @name', array('@name' => $library->label()));
  }

  public function content() {
    $build = array(
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    );
    return $build;
  }

}
