const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('vio-customer-price-customer', 'vio-customer-price-card', {
    created() {
        this.customerId = this.$route.params.id;
    },
    methods: {
        getList() {
            let criteria = new Criteria(this.page, this.limit);
            criteria
                .addAssociation('customer')
                .addAssociation('product')
                .addFilter(
                    Criteria.equals('customerId', this.$route.params.id)
                );

            this.repository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.customerPrices = result;
                });
        },
    },
});
