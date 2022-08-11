import './init/orlen-credentials.init';
import './init/orlen-label.init';
import './init/orlen-package.init';
import './bitbag-orlen-pickup-settings-base';

Shopware.Module.register('bitbag-orlen-pickup', {
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
            component: 'bitbag-orlen-pickup-settings-base',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
            },
        },
    },
    settingsItem: {
        group: 'plugins',
        to: 'bitbag.orlen.pickup.settings.index',
        iconComponent: 'bitbag-orlen-pickup-settings-icon',
        backgroundEnabled: false,
    },
});
