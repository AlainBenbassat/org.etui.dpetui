<?php

namespace Civi\DataProcessor\FieldOutputHandler\Qrcodecheckin;

use CRM_Dpetui_ExtensionUtil as E;

use Civi\DataProcessor\Source\SourceInterface;
use Civi\DataProcessor\FieldOutputHandler\HTMLFieldOutput;
use Civi\DataProcessor\FieldOutputHandler\AbstractSimpleSortableFieldOutputHandler;
use Civi\DataProcessor\FieldOutputHandler\AbstractFieldOutputHandler;
use Civi\DataProcessor\DataSpecification\FieldSpecification;

class QrCodeOutputHandler extends AbstractFieldOutputHandler {
  /**
   * @var \Civi\DataProcessor\Source\SourceInterface
   */
  protected $dataSource;

  /**
   * @var FieldSpecification
   */
  protected $participantId;

  /**
   * @var FieldSpecification
   */
  protected $outputFieldSpecification;

  protected function getFieldTitle() {
    return E::ts('QR-Code');
  }

  /**
   * Initialize the processor
   *
   * @param String $alias
   * @param String $title
   * @param array $configuration
   * @param \Civi\DataProcessor\ProcessorType\AbstractProcessorType $processorType
   */
  public function initialize($alias, $title, $configuration) {
    [,$this->participantId] = $this->initializeField($configuration['field'], $configuration['datasource'], $alias);
    $this->outputFieldSpecification = new FieldSpecification($this->participantId->name, 'String', $title, null, $alias);
  }

  /**
   * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  public function getOutputFieldSpecification() {
    return $this->outputFieldSpecification;
  }

  /**
   * Returns the data type of this field
   *
   * @return String
   */
  protected function getType() {
    return 'String';
  }

  public function formatField($rawRecord, $formattedRecord) {
    $participantId = $rawRecord[$this->participantId->alias];
    $qrCode = \CRM_Dpetui_QR::getCode($participantId);
    $formattedValue = new HTMLFieldOutput($qrCode);
    $formattedValue->setHtmlOutput($qrCode);
    return $formattedValue;
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Field/Configuration/Dpetui/QrCodeOutputHandler.tpl";
  }

  /**
   * Returns true when this handler has additional configuration.
   *
   * @return bool
   */
  public function hasConfiguration() {
    return true;
  }

  public function buildConfigurationForm(\CRM_Core_Form $form, $field= []) {
    $fieldSelect = \CRM_Dataprocessor_Utils_DataSourceFields::getAvailableFieldsInDataSources($field['data_processor_id']);

    $form->add('select', 'participant_id_field', E::ts('Participant ID Field'), $fieldSelect, true, [
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
    ]);

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      $defaults = [];
      if (isset($configuration['field']) && isset($configuration['datasource'])) {
        $defaults['participant_id_field'] = $configuration['datasource'] . '::' . $configuration['field'];
      }
      $form->setDefaults($defaults);
    }
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    [$datasource, $field] = explode('::', $submittedValues['participant_id_field'], 2);
    $configuration['field'] = $field;
    $configuration['datasource'] = $datasource;
    return $configuration;
  }

}
