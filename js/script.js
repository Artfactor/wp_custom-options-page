jQuery(document).ready(function($) {
  $('.add_new_field').on('submit', function(){
    let input  = $('.new_field'),
    name = $('[name="name"]').val(),
    type = $('[name="type"]').val(),
    result = JSON.stringify({name: name, type: type, value: ''});
    input.val(result);
  })

  $('.remove-option').on('click', function(e){
    e.preventDefault();
    var data = {
			action: 'remove_option',
			name: $(this).val()
    },
    tr = $(this).parents('tr');
    $.post(ajaxurl, data, function(response) {
      if(response == 1){
        tr.remove();
      }
      else{
        console.error('Ошибка!');
      }
    });
  });

  $('.edit_options').on('submit', function(e){
    $('.text_value').each(function(){
      let parent, jsonValueElement, currentValue, newValue, newJsonValue;
      parent = $(this).parents('tr');
      jsonValueElement = parent.find('.json_value');
      try{
        currentValue = JSON.parse(jsonValueElement.val());
      }
      catch (err){
        console.error(err);
        return true;
      }
      newValue = currentValue;
      newValue.value = $(this).val();
      newJsonValue = JSON.stringify(newValue);
      jsonValueElement.val(newJsonValue);
    });
  })

  
});