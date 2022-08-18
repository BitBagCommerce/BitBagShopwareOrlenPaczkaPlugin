const { ApiService } = Shopware.Classes;

export default class OrlenOriginOfficeService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'bitbag-orlen-paczka-plugin') {
        super(httpClient, loginService, apiEndpoint);
    }

    async getOriginOffices(salesChannelId = '') {
        return await this.httpClient.post(
            `_action/${this.getApiBasePath()}/origin-offices`,
            {
                salesChannelId
            },
            { headers: this.getBasicHeaders() }
        );
    }
}
