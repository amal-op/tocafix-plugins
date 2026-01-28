const { ApiService, Service, Application } = Shopware;

ApiService.register('vioCustomerPriceService', {
    contentType: 'application/json',

    httpClient() {
        return Application.getContainer('init')['httpClient'];
    },
    loginService() {
        return Service('loginService');
    },

    async import(importFile) {
        const formData = new FormData();
        formData.append('file', importFile);
        return this.httpClient().post(
            '/_action/vio_customer_price/import',
            formData,
            {
                headers: this.getBasicHeaders(),
            }
        );
    },

    getBasicHeaders(additionalHeaders = {}) {
        const basicHeaders = {
            Accept: this.contentType,
            Authorization: `Bearer ${this.loginService().getToken()}`
        };

        return Object.assign({}, basicHeaders, additionalHeaders);
    },
});
