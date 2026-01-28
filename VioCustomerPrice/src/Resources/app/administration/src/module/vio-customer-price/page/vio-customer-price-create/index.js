const { Component } = Shopware;

Component.extend('vio-customer-price-create', 'vio-customer-price-detail', {
    methods: {
        getCustomerPrice() {
            this.customerPrice = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.customerPrice, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({
                        name: 'vio.customer.price.detail',
                        params: { id: this.customerPrice.id },
                    });
                })
                .catch((exception) => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: this.$t('vio-customer-price.detail.errorTitle'),
                        message: exception,
                    });
                });
        },
    },
});
