/*!
 * bootstrap-fileinput v5.5.0
 * http://plugins.krajee.com/file-input
 *
 * Font Awesome 5.x icon theme configuration for bootstrap-fileinput. Requires font awesome 5 assets to be loaded.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2022, Kartik Visweswaran, Krajee.com
 *
 * Licensed under the BSD-3-Clause
 * https://github.com/kartik-v/bootstrap-fileinput/blob/master/LICENSE.md
 */
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'],factory);
    } else if (typeof module === 'object' && typeof module.exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    "use strict";

    $.fn.fileinputThemes.fa5 = {
        fileActionSettings: {
            removeIcon: '<i class="fa fa-trash-alt"></i>',
            uploadIcon: '<i class="fa fa-upload"></i>',
            uploadRetryIcon: '<i class="fa fa-cloud-arrow-up"></i>',
            downloadIcon: '<i class="fa fa-download"></i>',
            rotateIcon: '<i class="fa fa-redo"></i>',
            zoomIcon: '<i class="fa fa-search-plus"></i>',
            dragIcon: '<i class="fa fa-arrows-alt"></i>',
            indicatorNew: '<i class="fa fa-plus-circle text-warning"></i>',
            indicatorSuccess: '<i class="fa fa-check-circle text-success"></i>',
            indicatorError: '<i class="fa fa-exclamation-circle text-danger"></i>',
            indicatorLoading: '<i class="fa fa-hourglass text-muted"></i>',
            indicatorPaused: '<i class="fa fa-pause text-info"></i>'
        },
        layoutTemplates: {
            fileIcon: '<i class="fa fa-file kv-caption-icon"></i> '
        },
        previewZoomButtonIcons: {
            prev: '<i class="fa fa-chevron-left"></i>',
            next: '<i class="fa fa-chevron-right"></i>',
            rotate: '<i class="fa fa-redo"></i>',
            toggleheader: '<i class="fa fa-fw fa-arrows-alt-v"></i>',
            fullscreen: '<i class="fa fa-expand-arrows-alt"></i>',
            borderless: '<i class="fa fa-fw fa-external-link-alt"></i>',
            close: '<i class="fa fa-fw fa-times"></i>'
        },
        previewFileIcon: '<i class="fa fa-file"></i>',
        browseIcon: '<i class="fa fa-folder-open"></i>',
        removeIcon: '<i class="fa fa-trash-alt"></i>',
        cancelIcon: '<i class="fa fa-ban"></i>',
        pauseIcon: '<i class="fa fa-pause"></i>',
        uploadIcon: '<i class="fa fa-upload"></i>',
        msgValidationErrorIcon: '<i class="fa fa-exclamation-circle"></i> '
    };
}));
