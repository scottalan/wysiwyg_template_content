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
  public function form(array $form, FormStateInterface $form_state, CategoryInterface $wysiwyg_template_category = NULL) {
    /* @var \Drupal\wysiwyg_template_content\Entity\TemplateContent $wysiwyg_template */
    $wysiwyg_template = $this->entity;

    $category_storage = $this->entityManager->getStorage('wysiwyg_template_category');
    $category = $category_storage->load($wysiwyg_template->bundle());

    $form_state->set(['wysiwyg_template_content', 'wysiwyg_template_category'], $category);

//    $form['category'] = [
//      '#type' => 'entity_autocomplete',
//      '#title' => t('Find a category'),
//      '#target_type' => 'wysiwyg_template_category',
//      '#selection_settings' => [
//        'match_operator' => 'CONTAINS',
//      ],
//      '#required' => TRUE,
//    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $wysiwyg_template->label(),
      '#description' => $this->t('Select a name for this template.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $wysiwyg_template->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\wysiwyg_template_content\Entity\TemplateContent::load',
      ),
      '#disabled' => !$wysiwyg_template->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#default_value' => $wysiwyg_template->getDescription(),
      '#title' => $this->t('Description'),
      '#description' => $this->t('A description to be shown with the template.'),
    ];

    $form['body'] = [
      '#type' => 'text_format',
      '#format' => $wysiwyg_template->getFormat(),
      '#default_value' => $wysiwyg_template->getBody(),
      '#title' => $this->t('HTML template'),
      '#rows' => 10,
      '#required' => TRUE,
    ];

    $node_types = array_map(function ($item) {
      return $item->label();
    }, NodeType::loadMultiple());

    $form['node_types'] = [
      '#type' => 'checkboxes',
      '#default_value' => $wysiwyg_template->getNodeTypes(),
      '#title' => $this->t('Available for content types'),
      '#description' => $this->t('If you select no content type, this template will be available for all content types.'),
      '#access' => (bool) count($node_types),
      '#options' => $node_types,
    ];

    $form['category_id'] = array(
      '#type' => 'value',
      '#value' => $category->id(),
    );

    $form['template_id'] = array(
      '#type' => 'value',
      '#value' => $wysiwyg_template->id(),
    );

    return parent::form($form, $form_state, $wysiwyg_template);
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
    $wysiwyg_template = $this->getEntity();

    $form_state->setRedirect('entity.wysiwyg_template_category.collection');
    $status = $wysiwyg_template->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Successfully created the %label template.', [
          '%label' => $wysiwyg_template->label(),
        ]));
        break;

      case SAVED_UPDATED:
        drupal_set_message($this->t('Successfully updated the %label template.', [
          '%label' => $wysiwyg_template->label(),
        ]));
        break;
    }

    if ($form_state->hasValue(['wysiwyg_template_content', 'wysiwyg_template_category'])) {
      $yes = TRUE;
    }
    $category_value1 = $form_state->getValue('category_id');
    $category_value = $form_state->get(['wysiwyg_template_content', 'wysiwyg_template_category']);

    $form_state->setValue('template_id', $this->entity->id());
    $form_state->set('template_id', $this->entity->id());
  }

}
