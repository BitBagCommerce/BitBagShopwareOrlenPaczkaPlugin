import './init/custom-api-service.init';
import './component/orlen-detail-card';
import './component/orlen-pickup-point-details';
import './component/orlen-pickup-point-details-create-package';
import './component/orlen-pickup-point-details-get-label';
import './extension/sw-order/sw-order-detail-base';
import './extension/sw-settings/sw-system-config';
import './init/orlen-origin-office.init';
import './init/orlen-credentials.init';
import './component/orlen-pickup-point-settings-base';
import './component/orlen-pickup-point-settings-icon';
import './service/api/orlen-paczka-api.service';

Shopware.Module.register('bitbag-orlen', {
    type: 'plugin',
    name: 'Orlen Paczka settings',
    title: 'Orlen Paczka settings',
    description: 'Orlen Paczka settings',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#FFD700',
    icon: 'default-action-settings',
    routes: {
        index: {
            component: 'orlen-pickup-point-settings-base',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
            },
        },
    },
    settingsItem: {
        group: 'plugins',
        to: 'bitbag.orlen.index',
        iconComponent: 'orlen-pickup-point-settings-icon',
        backgroundEnabled: false,
    },
});
