import template from './vio-customer-price-card.html.twig';
import './vio-customer-price-card.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('vio-customer-price-card', {
    template,

    inject: ['repositoryFactory', 'acl'],
    mixins: [Mixin.getByName('listing')],

    data() {
        return {
            repository: null,
            customerPrices: null,
            activeModal: '',
            productId: null,
            customerId: null,
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
            let columns = [
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
            return columns.filter((column) => {
                if (this.productId !== null && column.property === 'product') {
                    return false;
                }
                // noinspection RedundantIfStatementJS
                if (
                    this.customerId !== null &&
                    column.property === 'customer'
                ) {
                    return false;
                }
                return true;
            });
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
            this.repository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.customerPrices = result;
                    this.total = result.total;
                    this.selection = {};
                    this.isLoading = false;
                });
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
