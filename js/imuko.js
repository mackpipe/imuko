/**
* @file
*/
(function ($, Drupal) {
  // Retorna la respuesta al cliente en la creacion de usuario
  Drupal.AjaxCommands.prototype.guardar = function (ajax, response, status) {
    // El resultado del proceso de guardado de usuario se
    // muestra en una ventana modal
    const username = response.message.username
    const uid = response.message.uid
    const html = 'Se ha creado el usuario [' + username + '] con el Id [' + uid + ']'
    const $myDialog = $('<div>' + html + '</div>').appendTo('body')
    Drupal.dialog($myDialog, { title: 'Resultado' }).showModal()
  }

  $.validator.addMethod("lettersonly", function (value, element) {
    return this.optional(element) || /^[a-z]+$/i.test(value);
  }, "Solo se aceptan letras");

  // Valida el formulario de creacion de usuario
  $('#usuario-formulario').validate({
    rules: {
      mauricio_nombre: {
        required: true,
        minlength: 5,
        lettersonly: true
      }
    },
    messages: {
      mauricio_nombre: {
        required: 'El nombre del usuario es obligatorio',
        minlength: 'El nombre de usuario debe tener al menos 5 caracteres'
      }
    }
  })

})(jQuery, Drupal);
