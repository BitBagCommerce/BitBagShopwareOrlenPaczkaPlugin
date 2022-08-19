import template from './sw-system-config.html.twig';

const { Component } = Shopware;

Component.override('sw-system-config', {
    template,
    inject: ['orlenOriginOfficeService'],
    data() {
        return {
            loaded: false
        };
    },
    methods: {
        _getElementBind(element, props) {
            if (!this.loaded) {
                this.orlenOriginOfficeService.getOriginOffices('')
                    .then((response) => {
                        this.loaded = true;

                        element.config.options = response.data.options;
                    })
                    .catch(err => {
                        console.error(err);
                    });

                return this.getElementBind(element, props);
            }

            return this.getElementBind(element, props);
        },
        isOriginOfficeField(element) {
            return element.name === 'BitBagShopwareOrlenPaczkaPlugin.orlen.originOffice';
        }
    },
});
