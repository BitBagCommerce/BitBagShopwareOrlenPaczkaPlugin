import Plugin from 'src/plugin-system/plugin.class';

export default class OrlenPlugin extends Plugin {
    init() {
        this.assignEventListeners();
    }

    changePickupPoint(pickupPoint) {
        if (null === pickupPoint.type || 'ORLEN' !== pickupPoint.type) {
            console.error('Trying to select a non-Orlen pickup point');
        }

        const orlenPickupPointPni = document.querySelector('#orlen-pickup-point-pni');
        const orlenPickupPointCity = document.querySelector('#orlen-pickup-point-city');
        const orlenPickupPointName = document.querySelector('#orlen-pickup-point-name');
        const orlenPickupPointProvince = document.querySelector('#orlen-pickup-point-province');
        const orlenPickupPointStreet = document.querySelector('#orlen-pickup-point-street');
        const orlenPickupPointZipCode = document.querySelector('#orlen-pickup-point-zipCode');

        orlenPickupPointPni.value = pickupPoint.pni;
        orlenPickupPointCity.value = pickupPoint.city;
        orlenPickupPointName.value = pickupPoint.name;
        orlenPickupPointProvince.value = pickupPoint.province;
        orlenPickupPointStreet.value = pickupPoint.street;
        orlenPickupPointZipCode.value = pickupPoint.zipCode;
    }

    assignEventListeners() {
        const changePickupPointButton = document.querySelector('#orlen-change-point');
        const $this = this;

        changePickupPointButton.addEventListener('click', function (e) {

            PPWidgetApp.toggleMap({
                elementId: 'orlen-plugin-widget',
                callback: $this.changePickupPoint,
                payOnPickup: false,
                type: ['ORLEN']
            });
        });
    }
}
