import template from './sw-system-config.html.twig';

const { Component } = Shopware;

Component.override('sw-system-config', {
    template,

    inject: ['orlenOriginOfficeService'],

    data() {
        return {
            loaded: false
        }
    },

    methods: {
        async _getElementBind(element, props) {
            if (!this.loaded) {
                try {
                    const offices = await this.orlenOriginOfficeService.getOriginOffices('fc78301172b94eba8fad2012eae64cdb');


                    element.config.options = offices.data.options;
                    element.config.placeholder = offices.data.placeholder;
                    element.config.label = offices.data.label;

                    console.error(element.config);

                } catch (e) {
                        console.error(e);
                }

                this.loaded = true;

                return this.getElementBind(element, props);
            }


            // console.error(element, props);
            //
            // return this.getElementBind(element, props);
        },

        isOriginOfficeField(element) {
            return element.name === 'BitBagShopwareOrlenPaczkaPlugin.orlen.originoffice';
        }
    },
});
