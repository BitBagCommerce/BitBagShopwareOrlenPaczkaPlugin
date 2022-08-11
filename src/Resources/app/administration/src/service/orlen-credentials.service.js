const { ApiService } = Shopware.Classes;

export default class OrlenCredentialsService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'bitbag-orlen-paczka-plugin') {
        super(httpClient, loginService, apiEndpoint);
    }

    async checkCredentials(username, password, environment) {
        return await this.httpClient.post(
            `_action/${this.getApiBasePath()}/credentials/check`,
            {
                username,
                password,
                environment
            },
            { headers: this.getBasicHeaders() }
        );
    }

    async saveCredentials(username, password, environment, salesChannelId) {
        return await this.httpClient.post(
            `_action/${this.getApiBasePath()}/credentials/save`,
            {
                username,
                password,
                environment,
                salesChannelId
            },
            { headers: this.getBasicHeaders() }
        );
    }
}
