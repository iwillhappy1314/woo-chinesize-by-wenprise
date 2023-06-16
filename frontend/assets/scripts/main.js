import Distpicker from './distpicker';
import stateMap from './state-map';
import Alpine from 'alpinejs';

let swap = (o, r = {}) => Object.keys(o).map(k => r[o[k]] = k) && r;

document.addEventListener('alpine:init', () => {
  Alpine.data('wccnHandler', () => ({
    billingAddress : {
      'state'    : '',
      'state2'   : '',
      'city'     : '',
      'address_1': '',
    },
    shippingAddress: {
      'state'    : '',
      'state2'   : '',
      'city'     : '',
      'address_1': '',
    },
    selectState(state) {
      this.billingAddress.state2 = stateMap[state];
      this.shippingAddress.state2 = stateMap[state];

      console.log(state);
      console.log(stateMap[state]);
    },
  }));
});

window.Alpine = Alpine;

Alpine.start();

(function($) {

  'use strict';

  let billingPicker = new Distpicker('.wccn-billing-distpicker', {
    province: swap(stateMap)[$('#billing_state').val()],
    city    : $('#billing_city').val(),
    district: $('#billing_address_1').val(),
  });

  let shippingPicker = new Distpicker('.wccn-shipping-distpicker', {
    province: swap(stateMap)[$('#shipping_state').val()],
    city    : $('#shipping_city').val(),
    district: $('#shipping_address_1').val(),
  });

  let calculatorPicker = new Distpicker('.wccn-calc-distpicker', {
    province: swap(stateMap)[$('#calc_shipping_state').val()],
    city    : $('#calc_shipping_city').val(),
  });

  /**
   * 兼容 fr address book 插件
   */
  $(document).on('change.fabfw', '.fabfw-select-address-container [name="fabfw_address_billing_id"]', function(event) {
    var selectedField = $(event.target);
    var address = fabfw_select_address.addresses[selectedField.val()];

    if (address) {
      $('#wccn-state').val(swap(stateMap)[address.state]).change();
      $('#wccn-city').val(address.city).change();
      $('#wccn-area').val(address.address_1);
    }

  });

})(jQuery);