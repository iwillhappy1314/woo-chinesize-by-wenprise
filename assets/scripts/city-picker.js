(function($) {

    'use strict';

    var state_el = $('#billing_state, #shipping_state');
    var city_el = $('#billing_city, #shipping_city');
    var area_el = $('#billing_address_1, #shipping_address_1');

    var event = 'change';
    if (jQuery.fn.select2) {
        event = 'select2:select';
    }

    state_el.on(event, function() {
        var selected_state = $(this).val();
        var html = ['<option value="">选择城市</option>'];
        var default_city = city_el.data('default');

        console.log(selected_state);

        // 选择了省份
        if (selected_state && selected_state !== '') {
            var state_data = woo_chinesize_city_data.filter(function(item) {
                return item.id === selected_state;
            });

            state_data[0].children.forEach(function(city_data) {
                if (default_city === city_data.name) {
                    html.push('<option selected value="' + city_data.name + '">' + city_data.name + '</option>');
                } else {
                    html.push('<option value="' + city_data.name + '">' + city_data.name + '</option>');
                }
            });

            html = html.join('');

            city_el.find('option').remove();
            city_el.append(html).trigger('change');
        }
    });

    city_el.on(event, function() {
        var selected_city = $(this).val();
        var selected_state = state_el.val();
        var default_area = area_el.data('default');

        var html = ['<option value="">选择区/县</option>'];

        console.log(selected_city);

        var state_data = woo_chinesize_city_data.filter(function(item) {
            return item.id === selected_state;
        });

        if (!state_data) {
            state_data = woo_chinesize_city_data;
        }

        if (state_data.length !== 0 && selected_city && selected_city !== '') {
            var city_data = state_data[0].children.filter(function(item) {
                return item.name === selected_city;
            });

            city_data[0].children.forEach(function(city_data) {
                if (default_area === city_data.name) {
                    html.push('<option selected value="' + city_data.name + '">' + city_data.name + '</option>');
                } else {
                    html.push('<option value="' + city_data.name + '">' + city_data.name + '</option>');
                }
            });

            html = html.join('');

            area_el.find('option').remove();
            area_el.append(html).trigger('change');
        }
    });

})(jQuery);