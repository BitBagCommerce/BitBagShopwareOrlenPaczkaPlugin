import template from './orlen-pickup-point-details-create-package.html.twig';

const { Component, Mixin } = Shopware;

Component.register('orlen-pickup-point-details-create-package', {
    template,
    inject: ['OrlenPaczkaApiService'],
    mixins: [
        Mixin.getByName('notification')
    ],
    props: [
        'order'
    ],
    data() {
        return {
            showButton: true
        };
    },
    created() {
        const order = this.order;

        if (order?.extensions?.orlen?.packageId) {
            this.showButton = false;
        }
    },
    methods: {
        createPackage() {
            const orderId = this.order.id;

            this.OrlenPaczkaApiService.createPackage(orderId)
                .then(() => {
                    this.createNotificationSuccess({message: this.$tc('package.created')});

                    this.showButton = false;

                    this.$root.$emit('getLabel.hideButton', false);
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        const responseData = err.response.data;
                        if (responseData && responseData.errors && responseData.errors.length > 0) {
                            const error = responseData.errors[0];
                            if (error) {
                                let errorMessage = error.detail;
                                if (errorMessage.at(-1) === '.') {
                                    errorMessage = error.detail.slice(0, -1)
                                }

                                this.createNotificationError({
                                    message: this.$tc(errorMessage).replace('%s', orderId)
                                });
                            }
                        }
                    }
                });
        },
    }
});
