import template from './sw-order-detail-orlen-detail-card.html.twig';
import './sw-order-detail-orlen-detail-card.scss';

const { Component } = Shopware;

Component.register('sw-order-detail-orlen-detail-card', {
    template,
    props: [
        'order'
    ],
    data() {
        return {
            showCard: false,
        };
    },
    created() {
        const order = this.order;

        if (order.extensions && order.extensions.orlen && order.extensions.orlen.id) {
            this.showCard = true;
        }
    }
});
