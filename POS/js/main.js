import { POSConfig } from './config.js';
import { POSCore } from './core.js';
import { POSUI } from './ui.js';
import { POSPayment } from './payment.js';
import { POSItems } from './items.js';
import { POSUtils } from './utils.js';

$(document).ready(() => {
    const config = { ...POSConfig };
    
    const pos = new POSCore({
        ui: new POSUI(),
        payment: new POSPayment(config),
        items: new POSItems(config),
        utils: POSUtils,
        config: config
    });

    window.POS = pos; // Make POS instance globally available if needed
    pos.init();
});