Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'customers',
    key: 'vio_customer_price',
    roles: {
        viewer: {
            privileges: ['vio_customer_price:read'],
            dependencies: ['customer.viewer', 'product.viewer'],
        },
        editor: {
            privileges: ['vio_customer_price:update'],
            dependencies: ['vio_customer_price.viewer'],
        },
        creator: {
            privileges: ['vio_customer_price:create'],
            dependencies: [
                'vio_customer_price.viewer',
                'vio_customer_price.editor',
            ],
        },
        deleter: {
            privileges: ['vio_customer_price:delete'],
            dependencies: ['vio_customer_price.viewer'],
        },
    },
});
