# 1.2.3
- fix product name not displaying correctly in list view and detail view

# 1.2.4
- add acl rule handling

# 1.2.5
- change event subscriber to subscribe new, not deprecated, Events for detailpage

# 1.2.6

- refactor acl rule

# 1.2.7

- refactor product extending

# 1.2.8

- add collective entry

# 1.2.9

- enable multiselect delete

# 1.3.0

- add import / export profile for the import / export module

# 1.3.1

- add own import for CSV files

# 1.3.2

- make 6.4 compatible

# 1.4.0

- add discount

# 1.4.1

- fix uninstall issue, on remove column

# 1.4.2

- fix uninstall issue, on remove profile

# 1.4.3

- fix uninstall issue, on remove profile

# 1.4.4

- add api action to delete all customer prices of one customer

# 1.5.0

- make plugin compatible with Shopware 6.4

# 1.5.1

- consider multiple customers with the same customer number on csv import

# 1.5.2

- fix listing price issue

# 1.5.3

- fix issue on bin/console

# 1.5.4

- fix migration for multi db environments

# 1.5.5

- fix acl handling

# 1.5.6

- fix autoset of "to"-value on creating new prices

# 1.5.7

- invalidate product detail page cache, if customer price is changed

# 1.5.8

- add customer id to some cache keys

# 1.5.9

- add customer id to category cache keys

# 1.5.10

- some refactoring

# 1.5.11

- fix bug on customer search

# 1.6.0

- implement customer price inheritance

# 1.6.1

- add customer to cache key, only if he has customer prices- 

# 1.7.0

- add event for customer price calculation, so other plugins can influence the price
- refactor product criteria event handling and use `sales_channel.product.process.criteria` instead of manual events 

# 1.7.1
- `ProductPriceCalculator` implements now the `ResetInterface`, cause service is tagged with `kernel.reset` in shopware core

# 1.7.2
- Now also note the advanced prices as calculation base

# 1.7.3
- Bug fix for products without advanced prices

# 1.7.4
- Additonal Custom Field Card

# 1.7.5
- add return value to console command, so no error is thrown- 

# 1.8.0
- if only one customer price with discount is set, keep advanced prices and apply discount on them

# 1.8.1
- allow imports with empty price and empty discount column and don't set value to 0

# 1.8.2

- fix bug in custom field set renderer

# 1.8.3

- fix customer specific price relation and change side of CascadeDelete

# 1.9.0
- compatibility with shopware 6.5 established

# 1.9.1
- allow imports with empty price and empty discount column and don't set value to 0

# 1.9.2

- improve performance of customer price list in administration

# 1.9.3

- fix bug in custom field set renderer 

# 1.9.4

- fix customer specific price relation and change side of CascadeDelete

# 1.10.0
- allow to apply the discount on customer price itself
- 
# 1.11.0
- add option "If discount is set, use customer price as list price."

# 1.12.0

- make plugin compatible with Shopware 6.6

# 1.13.0
- add currency conversion

# 1.14.0
- fix issue with doctrine on uninstalling the plugin

# 1.15.0
- function "if only one customer price with discount is set, keep advanced prices and apply discount on them" can be deactivated

# 1.16.0
- add option to ignore the calculated prices and use product default price as calculation base

# 1.16.1
- fixed bug in the import export module

# 1.16.2
- fixed bug that prevented customer accounts from beeing able to be deleted

# 1.16.3
- fixed bug of customer prices card on customer detail page in the admin

# 1.16.4
- delete added column from customer table on uninstall

# 1.16.5
- get config per sales channel

# 1.17.0
- make plugin compatible with Shopware 6.7

# 1.17.1
- added customerprice entity to customFieldSet configuration

# 1.17.2
- fixed bug of csv import not able to import customer prices

# 1.18.0
- Added a configuration, which allows customer-specific prices to be viewed as net prices, gross prices are calculated automatically.

# 1.18.1
- refactored extensionkey to constant and assigned a prefix
- changes extensionKey from customerPrices to vioCustomerPrices