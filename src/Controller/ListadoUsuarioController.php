<?php

namespace Drupal\imuko\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ListadoUsuarioController.
 *
 * @package Drupal\imuko\Controller
 */
class ListadoUsuarioController extends ControllerBase {

  /**
   * Display.
   *
   * @return string
   *   Return Table form.
   */
  public function display() {
    // Crea el encabezado de la tabla.
    $header_table = [
      'id' => t('Id'),
      'name' => t('Name'),
    ];

    // Selecciona los registros de la tabla para ser mostrados.
    $query = \Drupal::database()->select('myusers', 'm');
    $query->fields('m', ['id','name']);
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(5);
    // Se incluye la opcion de paginacion a 10 registros.
    $results = $pager->execute()->fetchAll();
    // Se inicializa el vector de registros en vacio.
    $rows=[];
    // Se recorre los resultados obtenidos de la consulta.
    foreach($results as $data) {
      $rows[] = [
        'id' => $data->id,
        'name' => $data->name,
      ];
    }

    //Genera la tabla con el control de tabla proporcionado por Drupal
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No se encontraron usuarios'),
    ];
    // Genera el control de paginazacion.
    $form['pager'] = array (
      '#type' => 'pager'
    );

    return $form;
  }

}
