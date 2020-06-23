<?php

namespace Drupal\imuko\Import;

/**
 * Class MiFormulario.
 */
class AddImportContent {

  /**
   * Funcion que adiciona unpo por uno los usuarios de la importacion.
   */
  public function addImportContentItem($item, &$context) {
    $context['sandbox']['current_item'] = $item;
    $message = 'Creando ' . $item['name'];
    $results = [];
    imuko_create_user_custom($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }

  /**
   * Funcion de finalizacion de importacion.
   */
  public function addImportContentItemCallback($success, $results) {
    // Fianliza el proceso de importacion, informa el
    // estado de la importacion final.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'Un item procesado.',
        '@count Items procesados.'
      );
    }
    else {
      $message = t('Finalizo con errores.');
    }
    drupal_set_message($message);
  }

}

 /**
  * Esta funcion crea un usuario custom, es invocada pro el proceso de Batch.
  */
function imuko_create_user_custom($item) {
  $name = $item['name'];
  $valid = TRUE;
  // Valida que el usuario no exista.
  if ($name <> '') {
    $query = "SELECT id FROM {myusers} WHERE name = :n";
    $data = db_query($query, [':n' => $name])
      ->fetchField();
    if ($data) {
      $valid = FALSE;
    }
  }
  // Si el nombre no son letras.
  if (!ctype_alpha($name)) {
    $valid = FALSE;
  }
  if (strlen($name) < 5) {
    $valid = FALSE;
  }
  // Si pasa las validaciones se inserta el usuario.
  if ($valid) {
    // Inserta el registro en la base de datos.
    db_insert('myusers')
      ->fields([
        'name' => $name,
      ])
      ->execute();
  }
}
