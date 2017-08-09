$(function () {
    intelli.maintenance = function (button, params) {
        var start = 0;
        var limit = 1000;
        var total = 0;
        var progress = 0;
        var interval = 1000;
        var url = intelli.config.admin_url + '/portfolio/' + params.module + '/consistency.json';

        var $barHolder = $('#js-' + params.action + '-progress');
        var $bar = $('.progress-bar', $barHolder);
        var $button = $(button);
        var startText = $button.text();

        $barHolder.removeClass('hidden').addClass('active');
        $bar.css('width', '1%').text('');
        $button.prop('disabled', true);

        $.ajaxSetup({async: false});
        $.post(url, {action: 'count'}, function (response) {
            total = response.total;
        });

        var timer = setInterval(function () {
            $.post(url, {start: start, limit: limit, action: params.action}, function (data) {
            });

            start += limit;
            progress = Math.round(start / total * 100);

            if (start > total) {
                clearInterval(timer);
                $barHolder.removeClass('active').addClass('hidden');
                intelli.notifFloatBox({msg: _t('done'), type: 'notif', autohide: true});
                $button.text(startText).prop('disabled', false);
            }
            else {
                $bar.css('width', progress + '%');
                $button.text(progress + '%');
            }
        }, interval);

        $.ajaxSetup({async: true});
    };

    $('.js-start-maintenance-cmd').on('click', function (e) {
        intelli.maintenance(this, $(this).data());
    });
});