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

  var stateMap = {
    '云南省'     : 'CN1',
    '北京市'     : 'CN2',
    '天津市'     : 'CN3',
    '河北省'     : 'CN4',
    '山西省'     : 'CN5',
    '內蒙古自治区'  : 'CN6',
    '辽宁省'     : 'CN7',
    '吉林省'     : 'CN8',
    '黑龙江省'    : 'CN9',
    '上海市'     : 'CN10',
    '江苏省'     : 'CN11',
    '浙江省'     : 'CN12',
    '安徽省'     : 'CN13',
    '福建省'     : 'CN14',
    '江西省'     : 'CN15',
    '山东省'     : 'CN16',
    '河南省'     : 'CN17',
    '湖北省'     : 'CN18',
    '湖南省'     : 'CN19',
    '广东省'     : 'CN20',
    '广西壮族自治区' : 'CN21',
    '海南省'     : 'CN22',
    '重庆市'     : 'CN23',
    '四川省'     : 'CN24',
    '贵州省'     : 'CN25',
    '陕西省'     : 'CN26',
    '甘肃省'     : 'CN27',
    '青海省'     : 'CN28',
    '宁夏回族自治区' : 'CN29',
    '澳门特别行政区' : 'CN30',
    '西藏自治区'   : 'CN31',
    '新疆维吾尔自治区': 'CN32',
  };

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

  $('.wccn-billing-distpicker').distpicker({
    province: swap(stateMap)[$('#billing_state').val()],
    city    : $('#billing_city').val(),
    district: $('#billing_address_1').val(),
  });

  $('.wccn-shipping-distpicker').distpicker({
    province: swap(stateMap)[$('#shipping_state').val()],
    city    : $('#shipping_city').val(),
    district: $('#shipping_address_1').val(),
  });

  $('.wccn-calc-distpicker').distpicker({
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