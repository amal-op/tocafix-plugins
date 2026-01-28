const {Application} = Shopware;
Application.addServiceProviderDecorator('customFieldDataProviderService', (customFieldService) => {
    customFieldService.addEntityName('customer_price');
    return customFieldService;
});
