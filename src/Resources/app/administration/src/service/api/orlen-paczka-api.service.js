const ApiService = Shopware.Classes.ApiService;

class OrlenPaczkaApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '') {
        super(httpClient, loginService, apiEndpoint);
    }

    createPackage(orderId) {
        const apiRoute = `${this.getApiBasePath()}/_action/bitbag-orlen-paczka-plugin/package/${orderId}`;

        return this.httpClient
            .post(apiRoute, {}, {
                headers: this.getBasicHeaders()
            })
            .then((response) => {
                if (201 === response.status) {
                    return ApiService.handleResponse(response);
                }
            });
    }
}

export default OrlenPaczkaApiService;
