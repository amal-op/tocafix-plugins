import './acl';
import './page/vio-customer-price-list';
import './page/vio-customer-price-card/component/vio-customer-price-collective-entry';
import './page/vio-customer-price-card/component/vio-customer-price-upload-modal';
import './page/vio-customer-price-card';
import './page/vio-customer-price-detail';
import './page/vio-customer-price-create';
import './page/vio-customer-price-product';
import './page/vio-customer-price-customer';

const { Module } = Shopware;

Module.register('vio-customer-price', {
    type: 'plugin',
    name: 'CustomerPrices',
    title: 'vio-customer-price.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#F07800',
    icon: 'regular-money-bill',

    routes: {
        list: {
            component: 'vio-customer-price-list',
            path: 'list',
            meta: {
                privilege: 'vio_customer_price.viewer',
            },
        },
        detail: {
            component: 'vio-customer-price-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'vio.customer.price.list',
                privilege: 'vio_customer_price.editor',
            },
        },
        create: {
            component: 'vio-customer-price-create',
            path: 'create',
            meta: {
                parentPath: 'vio.customer.price.list',
                privilege: 'vio_customer_price.creator',
            },
        },
    },

    navigation: [
        {
            id: 'vio-customer-price',
            label: 'vio-customer-price.general.mainMenuItemGeneral',
            color: '#F07800',
            path: 'vio.customer.price.list',
            icon: 'regular-money-bill',
            parent: 'sw-customer',
            privilege: 'vio_customer_price.viewer',
        },
    ],

    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'vio.customer.price.product',
                path: '/sw/product/detail/:id/customprice',
                component: 'vio-customer-price-product',
                meta: {
                    parentPath: 'sw.product.index',
                },
            });
        } else if (currentRoute.name === 'sw.customer.detail') {
            currentRoute.children.push({
                name: 'vio.customer.price.customer',
                path: '/sw/customer/detail/:id/customprice',
                component: 'vio-customer-price-customer',
                meta: {
                    parentPath: 'sw.customer.index',
                },
            });
        }
        next(currentRoute);
    },
});
