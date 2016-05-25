<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Form\TemplateContentForm.
 */

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\wysiwyg_template_content\CategoryInterface;

/**
 * Defines a class that builds the Template Form.
 */
class TemplateContentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state, CategoryInterface $template_category = NULL) {
    /* @var \Drupal\wysiwyg_template_content\Entity\TemplateContent $template */
    $template = $this->entity;

    $category_storage = $this->entityManager->getStorage('wysiwyg_template_category');
    $category = $category_storage->load($template->bundle());

    $form_state->set(['wysiwyg_template_content', 'wysiwyg_template_category'], $category);

    $form['category_id'] = array(
      '#type' => 'value',
      '#value' => $category->id(),
    );

    $form['template_id'] = array(
      '#type' => 'value',
      '#value' => $template->id(),
    );

    return parent::form($form, $form_state, $template);
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, FormStateInterface $form_state) {
    $template = parent::buildEntity($form, $form_state);

    // Prevent leading and trailing spaces in term names.
    $template->setName(trim($template->getName()));

    // Assign parents with proper delta values starting from 0.
//    $template->parent = array_keys($form_state->getValue('parent'));

    return $template;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface $template */
    $template = $this->getEntity();

    $form_state->setRedirect('entity.wysiwyg_template_category.collection');
    $status = $template->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Successfully created the %label template.', [
          '%label' => $template->label(),
        ]));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('Successfully updated the %label template.', [
          '%label' => $template->label(),
        ]));
        break;
    }

    $form_state->setValue('template_id', $this->entity->id());
    $form_state->set('template_id', $this->entity->id());
  }

}
