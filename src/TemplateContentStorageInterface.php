<?php

namespace Drupal\wysiwyg_template_content;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Defines an interface for wysiwyg template library entity storage classes.
 */
interface TemplateContentStorageInterface extends ContentEntityStorageInterface {

  /**
   * Finds all terms in a given vocabulary ID.
   *
   * @param string $lid
   *   Library id to retrieve templates for.
   */
  public function loadLibraryTemplates($lid);

}
