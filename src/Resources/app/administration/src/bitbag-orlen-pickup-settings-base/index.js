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

            const createMissingFieldNotification = (name) => {
                return this.createNotificationError({
                    title: this.$tc('config.field.missing'),
                    message: this.$tc('config.field.missing') + ': ' + this.$tc('config.field.' + name)
                })
            }

            const username = getConfigValue('username');
            const password = getConfigValue('password');
            const environment = getConfigValue('environment');

            try {
                await this.orlenCredentialsService.checkCredentials(username, password, environment);

                this.createNotificationSuccess({
                    message: this.$tc('config.saved')
                });
            } catch (e) {
                const field = e.response.data.errors[0].detail;
                createMissingFieldNotification(field);
            }
        },

        saveCredentials() {
            this.$refs.systemConfig.saveAll();

            this.createNotificationSuccess({message: this.$tc('config.saved')});
        }
    }
});
