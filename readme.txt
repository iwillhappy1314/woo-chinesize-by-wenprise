# WooCommerce Chinesize by Wenprise#
Contributors: iwillhappy1314
Donate link: https://www.wpzhiku.com/
Tags: WooCommerce, 省市关联, checkout, address
Requires PHP: 5.6.0
Requires at least: 4.7
Tested up to: 6.2
WC requires at least: 3.5
WC tested up to: 5.0
Stable tag: 1.3.1
License: GPL-2.0+

优化中国使用体验，实现省市区关联、订单模版优化等。

## Description ##

为了使 WooCommerce 更符合中国人的使用体验，WooCommerce Chinesize 对 WooCommerce 做了以下优化：

* 重新整理账单、结账、地址字段，把按照「省份 -> 城市 -> 详细地址」重新整理地址字段，修改「省/直辖市/自治区」为「省份」，修改「市」为「城市」，
* 实现省市区三级关联选择
* 移除地址第二行，在中国，详细地址是现在一行的、修改「街道地址」为「详细地址」
* 移除姓名中的「姓氏」字段，中国人的姓名一般是写在一起的
* 移除「国家/地区」字段，因为默认是在中国使用，国家字段是不必要的
* 允许通过设置移除结账地址中的公司和邮编字段
* 重新设计符合中国用户习惯的订单列表页和详情页模版。
* 在订单列表中添加订单过滤器（按状态）
* 优化中国地址显示格式


## Frequently Asked Questions ##

### 是否支持地址簿插件 ###

支持修改过的 Fr Address Book For Woocommerce 插件，请从[Github仓库](https://github.com/iwillhappy1314/fr-address-book-for-woocommerce) 下载并手动安装。


### Support 技术支持 ###

Email: amos@wpcio.com

## Installation ##

1. 上传插件到`/wp-content/plugins/` 目录，或在 WordPress 安装插件界面搜索 "WooCommerce Chinesize"，点击安装。
2. 在插件管理菜单激活插件

## Screenshots ##

1. 符合中国用户习惯的结账地址字段
2. 订单过滤器和优化后的订单列表

## Upgrade Notice ##

更新之前，请先备份数据库。

## Frequently Asked Questions ##

## Screenshots ##

## Changelog ##

### 1.3.0###

* 优化省市区选择实现方法，兼容性更好

### 1.2.1###

* Bugifx

### 1.2.0 ###
* 省市二级选择改为省市区三级选择
* 优化地址显示格式
* 兼容 Fr Address Book For Woocommerce 地址簿插件
* 增加禁用Select2的选项

### 1.1.2 ###
* 添加是否启用中国化地址的选项

### 1.1.0 ###
* 允许通过设置隐藏结账地址中的公司和邮编字段
* 允许覆盖我的账户中的订单列表模版
* 允许覆盖我的账户中的订单详情模版
* 允许在订单列表中添加订单过滤器

### 1.0.3 ###
* Bugs fix

### 1.0.2 ###
* Bugs fix

### 1.0.1 ###
* Bugs fix

### 1.0 ###
* 初次发布