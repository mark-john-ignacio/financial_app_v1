var MyApp = MyApp || {};

(function($) {
    'use strict';

    $(document).ready(function() {
        var apiURL = $('#frmpos').data('api-url');
        console.log

        // Initialize iCheck 
        if ($(".ichecks").length) {
            console.log("ichecks")
            $(".ichecks input").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        }

        // Cache jQuery selectors
        const $xcompute = $('.xcompute');

        // Event Listeners
        $("#btnPrintPdf").on("click", function(event) {
            event.preventDefault();
            var formData = getFormData("#frmpos");
            sendAjaxRequest(formData, apiURL);
        });

        $xcompute.on('input', handleInputRestriction);
        $xcompute.on('keypress', handleKeyPressRestriction);

        $('input[type="text"]').on('focus', function() {
            $(this).select();
        });

    });

    // Functions
    function getFormData(formSelector) {
        var formData = {};
        $(formSelector).serializeArray().forEach(function(item) {
            formData[item.name] = item.value;
        });
        console.log("Form data:", JSON.stringify(formData));
        return formData;
    }

    
    function sendAjaxRequest(formData, apiURL) {
        $.ajax({
            url: apiURL,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(formData),
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob, status, xhr) {
                handleBlobResponse(blob, xhr);
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", {xhr: xhr, status: status, error: error});
                handleError(xhr, status, error);
            }
        });
    }

    function handleBlobResponse(blob, xhr) {
        var filename = xhr.getResponseHeader('X-Filename') || "generated.pdf";
        var blobUrl = window.URL.createObjectURL(blob);
        openBlobUrlInNewTab(blobUrl, filename);
        revokeBlobUrl(blobUrl);
    }

    function openBlobUrlInNewTab(blobUrl, filename) {
        var newTab = window.open(blobUrl, '_blank');
        if (newTab) {
            newTab.onload = function() {
                newTab.document.title = filename;
            };
        } else {
            alert("Please allow popups for this website");
        }
    }

    function revokeBlobUrl(blobUrl) {
        setTimeout(function() {
            window.URL.revokeObjectURL(blobUrl);
        }, 5000);
    }

    function handleError(xhr, status, error) {
        console.error("Error status:", status);
        console.error("Error:", error);

        if (xhr.responseType === 'blob') {
            var reader = new FileReader();
            reader.onload = function() {
                try {
                    var errorResponse = JSON.parse(this.result);
                    console.error("Server error response:", errorResponse);
                    alert("Error: " + (errorResponse.message || "An unknown error occurred"));
                } catch (e) {
                    console.error("Unable to parse error response:", this.result);
                    alert("An error occurred: " + this.result);
                }
            };
            reader.onerror = function() {
                console.error("FileReader error:", reader.error);
                alert("An error occurred while reading the server response");
            };
            reader.readAsText(xhr.response);
        } else {
            console.error("Server response:", xhr.responseText);
            alert("An error occurred: " + xhr.responseText);
        }
    }

    function handleInputRestriction(event) {
        this.value = this.value.replace(/[^0-9.]/g, '');
    }

    function handleKeyPressRestriction(event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    }

    MyApp.sendAjaxRequest = sendAjaxRequest;
})(jQuery);