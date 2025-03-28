$(document).ready(function () {
    $('.cpf').mask('000.000.000-00');
    $('.ano').mask('0000');
    $('.moeda').maskMoney({
        prefix: 'R$ ',
        allowNegative: false,
        thousands: '.',
        decimal: ',',
        affixesStay: true
    });
});
