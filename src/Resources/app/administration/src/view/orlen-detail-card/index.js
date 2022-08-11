import template from './orlen-detail-card.html.twig';
import './orlen-detail-card.scss';

const { Component } = Shopware;

Component.register('orlen-detail-card', {
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
