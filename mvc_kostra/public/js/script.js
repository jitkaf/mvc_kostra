$(function () {

    // pro vsechny formulare jsem zapl bootstrapValidator..tj vsechny polozky musi byt vyplneny vsude
    $('form').bootstrapValidator({
        message: 'Vyplňte prosím',
        excluded: [':disabled']
    });

});