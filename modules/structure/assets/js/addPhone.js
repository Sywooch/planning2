$('.add-phone').click(function(){
    var newPhone = $('.'+$(this).attr('id')+'.template').clone().removeClass('template').removeAttr('style').removeAttr('id');
    var input = newPhone.find('input[type=text]').removeAttr('id').uniqueId();
    var hidden = newPhone.find('input[type=hidden]');
    input.attr('name', 'Employee[phones]['+input.attr('id')+'][phone]');
    hidden.attr('name', 'Employee[phones]['+input.attr('id')+'][type]');
    newPhone.appendTo('#field-phones');
    $('#'+input.attr('id')).inputmask(window[input.data('pluginInputmask')]);
    return false;
});

$('.delete-phone').live('click', function(){
    alert($(this).parent('.input-group').html());
    return false;
});