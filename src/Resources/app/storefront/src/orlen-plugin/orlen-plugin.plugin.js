import Plugin from 'src/plugin-system/plugin.class';

export default class OrlenPlugin extends Plugin {
    init() {
        this.assignEventListeners();
        this.checkIfPickupPointIsSelected();
    }

    checkIfPickupPointIsSelected() {
        const confirmFormSubmitButtonEl = document.getElementById('confirmFormSubmit');
        const pointNameNotSelectedMessageEl = document.querySelector('.point-name-not-selected-message');
        const orlenPickupPointPni = document.querySelector('#orlen-pickup-point-pni');
        const orlenPickupPointCity = document.querySelector('#orlen-pickup-point-city');
        const orlenPickupPointName = document.querySelector('#orlen-pickup-point-name');
        const orlenPickupPointProvince = document.querySelector('#orlen-pickup-point-province');
        const orlenPickupPointStreet = document.querySelector('#orlen-pickup-point-street');
        const orlenPickupPointZipCode = document.querySelector('#orlen-pickup-point-zipCode');

        if (confirmFormSubmitButtonEl) {
            confirmFormSubmitButtonEl.addEventListener('click', (e) => {
                if (
                    !orlenPickupPointPni.value ||
                    !orlenPickupPointCity.value ||
                    !orlenPickupPointName.value ||
                    !orlenPickupPointProvince.value ||
                    !orlenPickupPointStreet.value ||
                    !orlenPickupPointZipCode.value
                ) {
                    e.preventDefault();

                    document.querySelector('.shipping-methods').scrollIntoView({
                        behavior: 'smooth'
                    });

                    pointNameNotSelectedMessageEl.classList.remove('hide');
                }
            });
        }
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

        this.updatePickupPointHints(pickupPoint);
    }

    assignEventListeners() {
        const changePickupPointButton = document.querySelector('#orlen-change-point');
        const $this = this;

        changePickupPointButton.addEventListener('click', function () {

            /*global PPWidgetApp */
            PPWidgetApp.toggleMap({
                elementId: 'orlen-plugin-widget',
                callback: $this.changePickupPoint.bind($this),
                payOnPickup: false,
                address: $this.getAddress(),
                type: ['ORLEN']
            });
        });
    }

    updatePickupPointHints(pickupPoint) {
        const pointNameNotSelectedMessageEl = document.querySelector('.point-name-not-selected-message');
        const pniHint = document.querySelector('#orlen-pickup-point-pni-hint');
        const cityHint = document.querySelector('#orlen-pickup-point-city-hint');
        const streetHint = document.querySelector('#orlen-pickup-point-street-hint');

        pniHint.innerHTML = pickupPoint.pni;
        cityHint.innerHTML = pickupPoint.city;
        streetHint.innerHTML = pickupPoint.street;

        pointNameNotSelectedMessageEl.classList.add('hide');
    }

    getAddress() {
        const streetAddress = document.querySelector('#orlen-pickup-point-street').value;
        const city = document.querySelector('#orlen-pickup-point-city').value;

        if ('' === streetAddress || '' === city) {
            return '';
        }

        return streetAddress + ', ' + city;
    }
}
