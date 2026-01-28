import template from './vio-customer-price-collective-entry.html.twig';
const { Component, Context } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register('vio-customer-price-collective-entry', {
    template,

    inject: ['repositoryFactory', 'acl'],

    props: {
        productId: {
            type: String,
            required: false,
            default: null,
        },

        customerId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            isLoading: false,
            actualProgress: 0,
            maxProgress: 0,
            price: 0.0,
            discount: 0.0,
            quantityStart: 0.0,
            productIds: [],
            customerIds: [],
            productCollection: null,
            customerCollection: null,
            showAlert: false,
        };
    },

    created() {
        this.createdComponent();
    },

    computed: {
        pricesRepository() {
            return this.repositoryFactory.create('vio_customer_price');
        },
    },

    methods: {
        createdComponent() {
            const productRepository = this.repositoryFactory.create('product');
            this.productCollection = new EntityCollection(
                productRepository.route,
                productRepository.entityName,
                Context.api
            );
            const customerRepository =
                this.repositoryFactory.create('customer');
            this.customerCollection = new EntityCollection(
                customerRepository.route,
                customerRepository.entityName,
                Context.api
            );
        },

        onChangeProducts(collection) {
            this.productCollection = collection;
            this.productIds = collection.getIds();
        },

        onChangeCustomers(collection) {
            this.customerCollection = collection;
            this.customerIds = collection.getIds();
        },

        onSave() {
            if (!Array.isArray(this.customerIds)) {
                this.customerIds = [];
            }
            if (!Array.isArray(this.productIds)) {
                this.productIds = [];
            }
            if (this.customerIds.length < 1 && this.customerId !== null) {
                this.customerIds.push(this.customerId);
            }
            if (this.productIds.length < 1 && this.productId !== null) {
                this.productIds.push(this.productId);
            }

            if (
                (this.price <= 0 && this.discount <= 0) ||
                this.productIds.length < 1 ||
                this.customerIds.length < 1
            ) {
                this.showAlert = true;
                return;
            }

            this.isLoading = true;
            this.showAlert = false;
            this.maxProgress = this.customerIds.length * this.productIds.length;

            let customerPrices = [];
            this.customerIds.forEach((customerId) => {
                this.productIds.forEach((productId) => {
                    customerPrices.push({
                        customerId: customerId,
                        productId: productId,
                        price: this.price,
                        discount: this.discount,
                        quantityStart: this.quantityStart,
                    });
                });
            });

            customerPrices.forEach((customerPriceData) => {
                const criteria = new Criteria();
                criteria
                    .addFilter(
                        Criteria.equals(
                            'productId',
                            customerPriceData.productId
                        )
                    )
                    .addFilter(
                        Criteria.equals(
                            'customerId',
                            customerPriceData.customerId
                        )
                    )
                    .addFilter(
                        Criteria.equals(
                            'quantityStart',
                            customerPriceData.quantityStart
                        )
                    );
                // search for existing prices
                this.pricesRepository
                    .search(criteria, Context.api)
                    .then((result) => {
                        let customerPrice;
                        // check if update or create needed
                        if (result.length > 0) {
                            customerPrice = result[0];
                        } else {
                            customerPrice = this.pricesRepository.create(
                                Context.api
                            );
                            customerPrice.customerId =
                                customerPriceData.customerId;
                            customerPrice.productId =
                                customerPriceData.productId;
                            customerPrice.quantityStart =
                                customerPriceData.quantityStart;
                        }
                        customerPrice.price = customerPriceData.price;
                        customerPrice.discount = customerPriceData.discount;
                        this.pricesRepository
                            .save(customerPrice, Context.api)
                            .then(() => {
                                this.actualProgress++;
                                if (this.actualProgress === this.maxProgress) {
                                    this.reset();
                                }
                            })
                            .catch((exception) => {
                                this.createNotificationError({
                                    title: this.$t(
                                        'vio-customer-price.detail.errorTitle'
                                    ),
                                    message: exception.message,
                                });
                            });
                    });
            });
        },

        reset() {
            this.productIds = [];
            this.customerIds = [];
            this.maxProgress = 0;
            this.actualProgress = 0;
            this.price = 0.0;
            this.quantityStart = 0;
            this.createdComponent();
            this.isLoading = false;
            this.showAlert = false;
            this.$emit('modal-close');
        },
    },
});
