import Distpicker from './distpicker/distpicker';
import stateMap from './state-map';
import Alpine from 'alpinejs';

let swap = (o, r = {}) => Object.keys(o).map(k => r[o[k]] = k) && r;

document.addEventListener('alpine:init', () => {
  Alpine.data('wccnHandler', () => ({
    init() {
      this.billingAddress.state = swap(stateMap)[$('#billing_state').val()];
      this.billingAddress.state2 = $('#billing_state').val();
      this.billingAddress.city = $('#billing_city').val();
      this.billingAddress.address_1 = $('#billing_address_1').val();

      this.shippingAddress.state = swap(stateMap)[$('#shipping_state').val()];
      this.shippingAddress.state2 = $('#shipping_state').val();
      this.shippingAddress.city = $('#shipping_city').val();
      this.shippingAddress.address_1 = $('#shipping_address_1').val();
    },
    billingAddress : {
      'state': '',
      'state2': '',
      'city': '',
      'address_1': '',
    },
    shippingAddress: {
      'state': '',
      'state2': '',
      'city': '',
      'address_1': '',
    },
    selectBillingState(state) {
      this.billingAddress.state2 = stateMap[state];
    },
    selectShippingState(state) {
      this.shippingAddress.state2 = stateMap[state];
    },
    selectBillingState2(state) {
      this.billingAddress.state = swap(stateMap)[state];
    },
    selectShippingState2(state) {
      this.shippingAddress.state = swap(stateMap)[state];
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

  //$('#billing_state').on('change', function(event) {
  //  new Distpicker('.wccn-billing-distpicker', {
  //    province: swap(stateMap)[$('#billing_state').val()],
  //    city    : $('#billing_city').val(),
  //    district: $('#billing_address_1').val(),
  //  });
  //});

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