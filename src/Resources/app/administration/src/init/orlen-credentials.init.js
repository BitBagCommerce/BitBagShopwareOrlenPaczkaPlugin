import OrlenCredentialsService from '../service/orlen-credentials.service';

Shopware.Application.addServiceProvider('orlenCredentialsService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');

    return new OrlenCredentialsService(initContainer.httpClient, container.loginService);
});
