import { POSConfig } from './config.js';
import { POSCore } from './core.js';
import { POSUI } from './ui.js';
import { POSPayment } from './payment.js';
import { POSItems } from './items.js';

$(document).ready(() => {
    // Initialize POS system
    const pos = new POSCore({
        ui: new POSUI(),
        payment: new POSPayment(),
        items: new POSItems(),
        config: POSConfig
    });
    pos.init();
});