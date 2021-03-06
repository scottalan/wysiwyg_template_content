<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Controller\WysiwygTemplateContentController.
 */

namespace Drupal\wysiwyg_template_content\Controller;

use Drupal\node\NodeTypeInterface;
use Drupal\wysiwyg_template\Controller\TemplateController;
use Drupal\wysiwyg_template_content\Entity\TemplateContent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the wysiwyg_template_content module.
 */
class TemplateContentController extends TemplateController {

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
