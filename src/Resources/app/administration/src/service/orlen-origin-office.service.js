const { ApiService } = Shopware.Classes;

export default class OrlenOriginOfficeService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'bitbag-orlen-paczka-plugin') {
        super(httpClient, loginService, apiEndpoint);
    }

    getOriginOffices(salesChannelId = '') {
        return this.httpClient.get(
            `_action/${this.getApiBasePath()}/origin-offices?salesChannelId=${salesChannelId}`,
            {headers: this.getBasicHeaders()}
        );
    }
}
