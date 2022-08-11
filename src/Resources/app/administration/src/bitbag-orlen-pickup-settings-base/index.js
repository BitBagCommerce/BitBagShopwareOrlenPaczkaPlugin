import template from './bitbag-orlen-pickup-settings-base.html.twig';

Shopware.Component.register('bitbag-orlen-pickup-settings-base', {
    template,
    inject: [
        'systemConfigApiService',
        'orlenCredentialsService'
    ],
    mixins: [
        'notification',
    ],
    data() {
        return {
            isLoading: true,
            pluginDomain: 'BitBagShopwareOrlenPaczkaPlugin.orlen',
            username: '',
            password: '',
            environment: '',
            salesChannel: null
        }
    },
    created() {
        this.isLoading = false;
    },
    methods: {
        checkCredentials() {
            const systemConfig = this.$refs.systemConfig;
            const actualConfigData = systemConfig.actualConfigData;
            const currentSalesChannelId = systemConfig.currentSalesChannelId;
            const dataPrefix = this.pluginDomain + '.inPost';

            console.error(actualConfigData[currentSalesChannelId]);

            let username = actualConfigData[currentSalesChannelId][dataPrefix + 'Username'];

            if (null === username || undefined === username) {
                username = actualConfigData.null[dataPrefix + 'Username'];
            }

            let password = actualConfigData[currentSalesChannelId][dataPrefix + 'Password'];

            if (null === password || undefined === password) {
                password = actualConfigData.null[dataPrefix + 'Password'];
            }

            let environment = actualConfigData[currentSalesChannelId][dataPrefix + 'Environment'];

            if (null === environment || undefined === environment) {
                environment = actualConfigData.null[dataPrefix + 'Environment'];
            }

            const values = {
                username,
                password,
                environment
            };


            // Check using the Orlen service
        },

        async saveCredentials() {
            await this.orlenCredentialsService.saveCredentials('', '', '', '');
        }
        // saveSystemConfig() {
        //     this.$refs.systemConfig.saveAll();
        //
        //     this.createNotificationSuccess({message: this.$tc('config.saved')});
        // }
    }
});
