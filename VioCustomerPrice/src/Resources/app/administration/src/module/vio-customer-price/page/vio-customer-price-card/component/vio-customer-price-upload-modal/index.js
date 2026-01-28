import template from './vio-customer-price-upload-modal.html.twig';
const { Component, Application, ApiService } = Shopware;

Component.register('vio-customer-price-upload-modal', {
    template,

    inject: ['repositoryFactory', 'acl'],

    data() {
        return {
            isLoading: false,
            importFile: null,
            importCount: null,
        };
    },

    created() {},

    computed: {
        httpClient() {
            return Application.getContainer('init').httpClient;
        },
    },

    methods: {
        upload() {
            if (this.importFile) {
                this.isLoading = true;
                this.importCount = null;
                ApiService.getByName('vioCustomerPriceService')
                    .import(this.importFile)
                    .then((result) => {
                        this.isLoading = false;
                        this.importFile = null;
                        this.importCount = result.data.count;
                    });
            }
        },
    },
});
