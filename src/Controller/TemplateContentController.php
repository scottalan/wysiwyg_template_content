<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Controller\WysiwygTemplateContentController.
 */

namespace Drupal\wysiwyg_template_content\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wysiwyg_template_content\Entity\TemplateContent;
use Drupal\wysiwyg_template_content\CategoryInterface;
use Drupal\wysiwyg_template_content\TemplateListBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default controller for the wysiwyg_template_content module.
 */
class TemplateContentController extends ControllerBase {

  /**
   * The template storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $template_storage;

  /**
   * Returns a form to create a template in a category.
   *
   * @param \Drupal\wysiwyg_template_content\CategoryInterface $wysiwyg_template_category
   *   The category we are adding the template to.
   *
   * @return array
   *   The template form.
   */
  public function addTemplate(CategoryInterface $wysiwyg_template_category) {
    $template = $this->entityManager()->getStorage('wysiwyg_template_content')->create(array('category_id' => $wysiwyg_template_category->id()));
    return $this->entityFormBuilder()->getForm($template);
  }

  public function categoryOverview(CategoryInterface $wysiwyg_template_category) {
    if ($wysiwyg_template_category) {
      $templates = $wysiwyg_template_category->getTemplates();
      // @todo: send these for render in a draggable list.
    }
  }

  public function listJson(NodeTypeInterface $node_type = NULL) {
    $templates = [
      // @todo Support images.
      'imagesPath' => FALSE,
    ];
    foreach (TemplateContent::loadByNodeType($node_type) as $template) {
      $json_template = new \stdClass();
      $json_template->title = $template->label();
      // @todo Images.
      // @see https://www.drupal.org/node/2692469
      $json_template->description = $template->getDescription();
      $json_template->html = $template->getBody();

      $templates['templates'][] = $json_template;
    }

    $templates = json_encode($templates);

    $script = <<<"EOL"
CKEDITOR.addTemplates( 'default', $templates);
EOL;

    $response = new Response($script);
    $response->headers->set('Content-Type', 'text/javascript');
    return $response;
  }

}
