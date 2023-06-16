export default {
  // Selects the districts automatically.
  // 0 -> Disable autoselect
  // 1 -> Autoselect province only
  // 2 -> Autoselect province and city only
  // 3 -> Autoselect all (province, city and district)
  autoselect: 0,

  // Show placeholder.
  placeholder: true,

  // Select value. Options: 'name' and 'code'
  valueType: 'name',

  // Defines the initial value of province.
  province: '选择省份',

  // Defines the initial value of city.
  city: '选择城市',

  // Defines the initial value of district.
  district: '选择区/县',
};
