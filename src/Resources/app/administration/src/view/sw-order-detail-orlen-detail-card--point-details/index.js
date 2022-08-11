import template from './sw-order-detail-orlen-detail-card--point-details.html.twig';

const { Component } = Shopware;

Component.register('sw-order-detail-orlen-detail-card--point-details', {
    template,
    props: [
        'order'
    ],
    created() {
        const order = this.order;

        this.removeOrlenDetailCardIfNotFoundOrlen(order);

        setTimeout(() => {
            this.setOrlenDetailsData(order);
        }, 1000);
    },
    methods: {
        setOrlenDetailsData(order) {
            const data = order.extensions.orlen;
            if (!data || !data.id) {
                return;
            }

            const pointNameEl = this.$refs.pointName;
            if (pointNameEl) {
                pointNameEl.textContent = data.pickupPointName;
            }

            const streetEl = this.$refs.street;
            if (streetEl) {
                streetEl.textContent = data.PickupPointStreet;
            }

            const postCodeEl = this.$refs.postCode;
            if (postCodeEl) {
                postCodeEl.textContent = data.pickupPointZipCode;
            }

            const cityEl = this.$refs.city;
            if (cityEl) {
                cityEl.textContent = data.pickupPointCity;
            }

            const provinceEl = this.$refs.province;
            if (provinceEl) {
                provinceEl.textContent = data.pickupPointProvince;
            }
        },
        removeOrlenDetailCardIfNotFoundOrlen(order) {
            if (!order || !order.extensions.orlen || !order.extensions.orlen.id) {
                const orlenDetailCardEl = this.$refs.orlenDetailsCard;
                if (orlenDetailCardEl) {
                    orlenDetailCardEl.remove();
                }
            }
        },
    }
});
