import Distpicker from './distpicker';
import stateMap from './state-map';

(function($) {

  'use strict';

  var viewData;

  viewData = {};

  $(function() {
    // Update the viewData object with the current field keys and values.
    function updateViewData(key, value) {
      viewData[key] = value;
    }

    // Register all bindable elements
    function detectBindableElements() {
      var bindableEls;

      bindableEls = $('[data-bind]');

      // Add event handlers to update viewData and trigger callback event.
      bindableEls.on('change', function() {
        var $this;

        $this = $(this);

        updateViewData($this.data('bind'), $this.val());

        $(document).trigger('updateDisplay');
      });

      // Add a reference to each bindable element in viewData.
      bindableEls.each(function() {
        updateViewData($(this));
      });
    }

    // Trigger this event to manually update the list of bindable elements, useful when dynamically loading form fields.
    $(document).on('updateBindableElements', detectBindableElements);

    detectBindableElements();
  });

  let swap = (o, r = {}) => Object.keys(o).map(k => r[o[k]] = k) && r;

  function updateDisplay() {
    var updateEls;

    updateEls = $('[data-update]');

    // update binded fields value.
    updateEls.each(function() {
      var addressType = $(this).data('update');
      var addressValue = viewData[addressType];

      if (stateMap.hasOwnProperty(addressValue) && ($(this).data('update') === 'billing-state' || $(this).data('update') === 'shipping-state')) {
        $(this).val(stateMap[addressValue]).change();
      } else {
        $(this).val(addressValue).change();
      }

    });

  }

  $(document).on('updateDisplay', updateDisplay);

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

  //$('.wccn-billing-distpicker').Distpicker({
  //  province: swap(stateMap)[$('#billing_state').val()],
  //  city    : $('#billing_city').val(),
  //  district: $('#billing_address_1').val(),
  //});

  //$('.wccn-shipping-distpicker').Distpicker({
  //  province: swap(stateMap)[$('#shipping_state').val()],
  //  city    : $('#shipping_city').val(),
  //  district: $('#shipping_address_1').val(),
  //});
  //
  //$('.wccn-calc-distpicker').Distpicker({
  //  province: swap(stateMap)[$('#calc_shipping_state').val()],
  //  city    : $('#calc_shipping_city').val(),
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