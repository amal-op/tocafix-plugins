import template from './vio-customer-price-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('vio-customer-price-list', {
    template,

    inject: ['repositoryFactory', 'acl'],
    mixins: [Mixin.getByName('listing')],

    data() {
        return {
            repository: null,
            customerPrices: null,
            activeModal: '',
            total: 0,
            isLoading: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        columns() {
            return [
                {
                    property: 'quantityStart',
                    dataIndex: 'quantityStart',
                    label: this.$t(
                        'vio-customer-price.list.columnQuantityStart'
                    ),
                    inlineEdit: 'number',
                    allowResize: true,
                },
                {
                    property: 'quantityEnd',
                    dataIndex: 'quantityEnd',
                    label: this.$t('vio-customer-price.list.columnQuantityEnd'),
                    inlineEdit: 'number',
                    allowResize: true,
                },
                {
                    property: 'price',
                    dataIndex: 'price',
                    label: this.$t('vio-customer-price.list.columnPrice'),
                    inlineEdit: 'number',
                    allowResize: true,
                },
                {
                    property: 'discount',
                    dataIndex: 'discount',
                    label: this.$t('vio-customer-price.list.columnDiscount'),
                    inlineEdit: 'number',
                    allowResize: true,
                },
                {
                    property: 'product',
                    dataIndex: 'product.name',
                    label: this.$t('vio-customer-price.list.columnProduct'),
                    allowResize: true,
                },
                {
                    property: 'customer',
                    dataIndex: 'customer.customerNumber',
                    label: this.$t('vio-customer-price.list.columnCustomer'),
                    allowResize: true,
                },
            ];
        },
    },

    methods: {
        getList() {
            this.isLoading = true;
            let criteria = new Criteria(this.page, this.limit);
            criteria
                .addAssociation('customer')
                .addAssociation('product')
                .addSorting(Criteria.sort('productId', 'ASC'))
                .addSorting(Criteria.sort('customerId', 'ASC'))
                .addSorting(Criteria.sort('quantityStart', 'ASC'));

            if (this.repository !== null) {
                this.repository
                    .search(criteria, Shopware.Context.api)
                    .then((result) => {
                        this.total = result.total;
                        this.selection = {};
                        this.customerPrices = result;
                        this.isLoading = false;
                    });
            }
        },

        openModal(value) {
            this.activeModal = value;
        },

        onCloseModal() {
            this.activeModal = '';
            this.getList();
        },
        updateTotal({ total }) {
            this.total = total;
        },
    },

    created() {
        this.repository = this.repositoryFactory.create('vio_customer_price');
        this.getList();
    },
});
