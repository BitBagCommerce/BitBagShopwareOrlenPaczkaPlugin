import template from './orlen-pickup-point-details.html.twig';

const { Component } = Shopware;

Component.register('orlen-pickup-point-details', {
    template,
    props: [
        'order',
    ],
    data() {
        return {
            showDetails: false,
            pickupPoint: {
                name: '',
                street: '',
                zipCode: '',
                city: '',
                province: '',
            },
        };
    },
    created() {
        const order = this.order;
        if (order && order.extensions.orlen && order.extensions.orlen.id) {
            this.setOrlenDetailsData(order);

            this.showDetails = true;
        }
    },
    methods: {
        setOrlenDetailsData(order) {
            const data = order.extensions.orlen;

            this.pickupPoint.name = data.pickupPointName;
            this.pickupPoint.street = data.pickupPointStreet;
            this.pickupPoint.zipCode = data.pickupPointZipCode;
            this.pickupPoint.city = data.pickupPointCity;
            this.pickupPoint.province = data.pickupPointProvince;
        },
    }
});
