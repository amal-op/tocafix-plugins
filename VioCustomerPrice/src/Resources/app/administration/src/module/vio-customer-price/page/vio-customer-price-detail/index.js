import template from './vio-customer-price-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('vio-customer-price-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [Mixin.getByName('notification')],

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    data() {
        return {
            customerPrice: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            customFieldSets: [],
        };
    },
    computed: {
        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        // sets the criteria used for your custom field set
        customFieldSetCriteria() {
            const criteria = new Criteria(1, null);
            criteria.addFilter(
                Criteria.equals('relations.entityName', 'customer_price')
            );
            criteria.getAssociation('customFields');

            return criteria;
        },
    },
    created() {
        this.repository = this.repositoryFactory.create('vio_customer_price');
        this.createdComponent();
        this.getCustomerPrice();
    },

    methods: {
        createdComponent() {
            this.$emit('update-loading', true);

            this.customFieldSetRepository
                .search(this.customFieldSetCriteria, Shopware.Context.api)
                .then((result) => {
                    this.customFieldSets = result;
                });
        },

        getCustomerPrice() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.customerPrice = entity;
                });
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.customerPrice, Shopware.Context.api)
                .then(() => {
                    this.getCustomerPrice();
                    this.isLoading = false;
                    this.processSuccess = true;
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('vio-customer-price.detail.errorTitle'),
                        message: exception.message,
                    });
                });
        },

        saveFinish() {
            this.processSuccess = false;
        },
    },
});
