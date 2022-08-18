import OrlenPaczkaApiService from '../service/api/orlen-paczka-api.service';

const Application = Shopware.Application;

Application.addServiceProvider('OrlenPaczkaApiService', (container) => {
    const initContainer = Application.getContainer('init');

    return new OrlenPaczkaApiService(initContainer.httpClient, container.loginService);
});
