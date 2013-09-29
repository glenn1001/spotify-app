$(window).load(function() {
    var email = 'Listen-2-achieve email';
    var password = 'Password';
    
    $('.email').val(email).css('color', 'grey');
    $('.password').val(password).css('color', 'grey');
    
    $('.email').on('focus', function() {
        if ($(this).val() == email) {
            $(this).val('').css('color', 'black');
        }
    });
    
    $('.password').on('focus', function() {
        if ($(this).val() == password) {
            $(this).val('').css('color', 'black');
        }
    });
    
    $('.email').on('blur', function() {
        if ($(this).val() == '') {
            $(this).val(email).css('color', 'grey');
        }
    });
    
    $('.password').on('blur', function() {
        if ($(this).val() == '') {
            $(this).val(password).css('color', 'grey');
        }
    });
});