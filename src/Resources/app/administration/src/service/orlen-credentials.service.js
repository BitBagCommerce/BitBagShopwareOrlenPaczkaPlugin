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
}
