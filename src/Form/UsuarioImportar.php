<?php

namespace Drupal\imuko\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class MiFormulario.
 */
class UsuarioImportar extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usuario_importar';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Permite al formulario enviar archivos
    $form['#attributes']['enctype'] = "multipart/form-data";
    // Campo de tipo archivo que valida que el archivo sea CSV
    $form['file'] = [
      '#type' => 'managed_file',
      '#title' => t('File'),
      '#upload_location' => 'public://file',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#default_value' => [2]
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->getValue(['file', 0]);
    if (!empty($fid)) {
      $file = File::load($fid);
      $file->setPermanent();
      $file->save();

      // se llama la funcion que convierte el archivo en un array,
      $data = $this->csvToArray(',', $file->getFileUri());
      foreach ($data as $row) {
        $operations[] = ['\Drupal\imuko\Import\addImportContent::addImportContentItem', [$row]];
      }
      // Se arma la estructura para enviar al proceso Batch
      $batch = [
        'title' => t('Importando Usuarios...'),
        'operations' => $operations,
        'init_message' => t('Importacion iniciada.'),
        'finished' => '\Drupal\imuko\Import\addImportContent::addImportContentItemCallback',
      ];
      // Inicia el proceso de Batch para la importacion.
      batch_set($batch);
    }
  }

  /**
   * Funcion que convierte los datos del CSV a un array.
   */
  public function csvToArray($delimiter, $filename = '') {
    // Lee el archivo y carga cada una de las lineas en al array.
    if (!file_exists($filename) || !is_readable($filename)) {
      return FALSE;
    }
    $header = NULL;
    $data = [];

    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        if (!$header) {
          $header = $row;
        }
        else {
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }

    return $data;
  }

}
