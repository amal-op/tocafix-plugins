const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('vio-customer-price-product', 'vio-customer-price-card', {
    created() {
        this.productId = this.$route.params.id;
    },

    methods: {
        getList() {
            let criteria = new Criteria(this.page, this.limit);
            criteria
                .addAssociation('customer')
                .addAssociation('product')
                .addFilter(Criteria.equals('productId', this.$route.params.id));

            this.repository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.customerPrices = result;
                });
        },
    },
});
