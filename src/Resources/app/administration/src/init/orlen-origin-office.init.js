import OrlenOriginOfficeService from "../service/orlen-origin-office.service";

Shopware.Application.addServiceProvider('orlenOriginOfficeService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');

    return new OrlenOriginOfficeService(initContainer.httpClient, container.loginService);
});
