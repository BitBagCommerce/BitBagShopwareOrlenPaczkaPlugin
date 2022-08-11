import OrlenCredentialsService from '../service/orlen-credentials.service';

Shopware.Application.addServiceProvider('orlenCredentialsService', () => {
    const initContainer = Shopware.Application.getContainer('init');

    return new OrlenCredentialsService(initContainer.httpClient, initContainer.loginService);
});
