<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Form\TemplateContentForm.
 */

namespace Drupal\wysiwyg_template_content\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\node\Entity\NodeType;
use Drupal\wysiwyg_template\Form\TemplateForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\wysiwyg_template\TemplateInterface;

/**
 * Defines a class that builds the Template Form.
 *
 * @package Drupal\wysiwyg_template_content\Form
 */
class TemplateContentForm extends ContentEntityForm {

  /**
   * TemplateContentForm constructor.
   *
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(EntityManagerInterface $entityManager) {
    parent::__construct($entityManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

  public function form(array $form, FormStateInterface $form_state) {
    $form =  parent::form($form, $form_state);

    /* @var \Drupal\wysiwyg_template_content\TemplateContentInterface $wysiwyg_template */
    $wysiwyg_template = $this->entity;

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
        'exists' => '\Drupal\wysiwyg_template\Entity\Template::load',
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
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\wysiwyg_template_content\TemplateContentInterface $template */
//    $entity = $this->getEntity();

    $form_state->setRedirect('entity.wysiwyg_template_content.collection');
    $status = $this->entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Successfully created the %label template.', [
          '%label' => $this->entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label template.', [
          '%label' => $this->entity->label(),
        ]));
    }
  }

}
