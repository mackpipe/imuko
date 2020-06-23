<?php

namespace Drupal\imuko\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\imuko\Ajax\CrearAjaxCommand;

/**
 * Class MiFormulario.
 */
class UsuarioFormulario extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usuario_formulario';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('nombre'),
      '#description' => $this->t('ingrese el nombre del usuario'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#ajax' => [
        'callback' => '::validateNameAjax',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Verificando Nombre...'),
        ],
      ],
      '#suffix' => '<span class="name-valid-message"></span>'
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::submitFormCustom',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Guardando ...',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $name = $field['name'];

    if (strlen($name) < 5) {
      $form_state->setErrorByName('name', $this->t('El nombre debe tener minimo 5 caracteres.'));
    }

    if (!ctype_alpha($name)) {
      $form_state->setErrorByName('name', $this->t('El nombre debe contener solo letras.'));
    }
    if ($name <> '') {
      $query = "SELECT id FROM {myusers} WHERE name = :n";
      $data = db_query($query, [':n' => $name])
        ->fetchField();
      if ($data) {
        $form_state->setErrorByName('name', $this->t('Ya existe un usuario con este nombre, por favor cambiarlo.'));
      }
    }
    parent::validateForm($form, $form_state);
  }


  /**
   * Ajax callback to validate the email field.
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function validateNameAjax(array &$form, FormStateInterface $form_state)
  {
    $valid = TRUE;
    $field = $form_state->getValues();
    $name = $field['name'];

    if (strlen($name) < 5) {
      $msj = 'El nombre debe tener minimo 5 caracteres';
      $valid = FALSE;
    }

    if (!ctype_alpha($name)) {
      $msj = 'El nombre debe contener solo letras';
      $valid = FALSE;
    }
    if ($name <> '') {
      $query = "SELECT id FROM {myusers} WHERE name = :n";
      $data = db_query($query, [':n' => $name])
        ->fetchField();
      if ($data) {
        $msj = 'Ya existe un usuario con este nombre, por favor cambiarlo';
        $valid = FALSE;
      }
    }

    $response = new AjaxResponse();
    if ($valid) {
      $css = ['border' => '1px solid green'];
      $message = $this->t('Nombre correcto.');
    } else {
      $css = ['border' => '1px solid red'];
      $message = $this->t($msj);
    }
    $response->addCommand(new CssCommand('#edit-name', $css));
    $response->addCommand(new HtmlCommand('.name-valid-message', $message));
    return $response;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function submitFormCustom(array $form, FormStateInterface $form_state)
  {

    $field = $form_state->getValues();
    $name = $field['name'];
    $id = db_insert('myusers')
      ->fields([
        'name' => $name,
      ])
      ->execute();

    $response = new AjaxResponse();
    $response->addCommand(
      new crearAjaxCommand($name, $id)
    );
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
