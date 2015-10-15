var system = require('system');
var env = system.env;

function loginIBM() {
    system.stdout.writeLine('Logging');

    page.onLoadFinished = downloadLink;
    page.evaluate(function (username, password) {
        document.getElementById('userID').value = username;
        document.getElementById('password').value = password;
        document.forms[1].submit();
    }, env['IBM_USERNAME'], env['IBM_PASSWORD']);
}

function downloadLink() {
    var link = page.evaluate(function () {
        return document.getElementsByClassName('ibm-download-link')[0].href;
    });

    if (!link) {
        system.stdout.writeLine('Link not found');
        phantom.exit(1);
        return false;
    }
    system.stdout.writeLine('Download link:');
    system.stdout.writeLine(link);
    phantom.exit(0);
}

system.stdout.writeLine('Starting');

var page = require('webpage').create();
page.settings.userAgent = 'Mozilla/5.0';

page.onResourceError = function (error) {
//    system.stderr.writeLine(JSON.stringify(error));
//    phantom.exit(1);
}
page.onLoadFinished = loginIBM;
page.open('https://www-01.ibm.com/marketing/iwm/iwm/web/reg/download.do?source=ifxids&S_TACT=109HF16W&lang=en_US&S_PKG=lin_' + env['DOWNLOAD_VERSION'] + '&cp=UTF-8&dlmethod=http');