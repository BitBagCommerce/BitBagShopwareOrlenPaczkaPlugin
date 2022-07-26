import template from './orlen-pickup-point-settings-base.html.twig';

Shopware.Component.register('orlen-pickup-point-settings-base', {
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
        async checkCredentials() {
            const systemConfig = this.$refs.systemConfig;
            const actualConfigData = systemConfig.actualConfigData;
            const currentSalesChannelId = systemConfig.currentSalesChannelId;

            const prefix = this.pluginDomain + '.';
            const getConfigValue = (name) => {
                const prefixedName = prefix + name;

                let configValue = actualConfigData[currentSalesChannelId][prefixedName];
                if (null === configValue || undefined === configValue) {
                    configValue = actualConfigData[null][prefixedName];
                }

                return configValue;
            };

            const username = getConfigValue('username');
            const password = getConfigValue('password');
            const environment = getConfigValue('environment');

            try {
                await this.orlenCredentialsService.checkCredentials(username, password, environment);

                this.createNotificationSuccess({
                    message: this.$tc('config.credentialsDataValid')
                });
            } catch (e) {
                const message = e.response.data?.errors[0]?.detail;
                this.createNotificationError({
                    message: this.$tc(message)
                });
            }
        },

        saveCredentials() {
            this.$refs.systemConfig.saveAll();

            this.createNotificationSuccess({message: this.$tc('config.saved')});
        }
    }
});
