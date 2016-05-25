<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template_content\Entity\TemplateContent.
 */

namespace Drupal\wysiwyg_template_content\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\wysiwyg_template_content\TemplateContentInterface;

/**
 * Defines the Template Content entity.
 *
 * @ContentEntityType(
 *   id = "wysiwyg_template_content",
 *   label = @Translation("Template Content"),
 *   bundle_label = @Translation("Category"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\wysiwyg_template_content\Form\TemplateContentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "wysiwyg_template_content",
 *   base_table = "wysiwyg_template_content_field_data",
 *   uri_callback = "wysiwyg_template_content_uri",
 *   admin_permission = "administer wysiwyg templates",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "template_id",
 *     "bundle" = "category_id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "wysiwyg_template_category",
 *   field_ui_base_route = "entity.wysiwyg_template_category.overview_form",
 *   links = {
 *     "canonical" = "/admin/template-categories/templates/{wysiwyg_template_content}",
 *     "edit-form" = "/admin/template-categories/templates/{wysiwyg_template_content}/edit",
 *     "delete-form" = "/admin/template-categories/templates/{wysiwyg_template_content}/delete",
 *     "reassign-form" = "/admin/template-categories/templates/{wysiwyg_template_content}/reassign",
 *     "collection" = "/admin/template-categories/templates"
 *   },
 *   permission_granularity = "bundle"
 * )
 */
class TemplateContent extends ContentEntityBase implements TemplateContentInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->get('template_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getCategory() {
    return $this->get('category_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getCategoryId() {
    return $this->get('category_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('description', $description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getBody() {
    if ($body = $this->get('body') && isset($body['value'])) {
      return $body['value'];
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setFormat($format) {
    if ($body = $this->get('body')) {
      $this->set($body['format'], $format);
      return $this;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat() {
    if ($body = $this->get('body') && isset($body['format'])) {
      return $body['format'];
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('label', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->label();
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Give the template a name.'))
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['body'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('HTML Template'))
      ->setDescription(t('The template content.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => -9,
      ))
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => -9,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setDescription(t('Describe the template.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -8,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -8,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['template_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Template Content ID'))
      ->setDescription(t('The template ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The template UUID.'))
      ->setReadOnly(TRUE);

    $fields['category_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Category'))
      ->setDescription(t('The category to which the template is assigned.'))
      ->setSetting('target_type', 'wysiwyg_template_category');

    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t('The weight of this template in relation to other templates.'))
      ->setDefaultValue(0);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the template was last edited.'))
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried roles by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

}
