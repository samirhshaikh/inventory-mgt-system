/**
 * Commonly used functions
 */

export default {
    getRandomId() {
        return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    },

    searchJsonObjects(
        json_object,
        search_field,
        search_val,
        return_first
    ) {
        return_first = typeof return_first === "undefined" ? true : return_first;

        let results = [];

        if (_.isArray(json_object)) {
            for (let i = 0; i < json_object.length; i++) {
                if (json_object[i][search_field] == search_val) {
                    if (return_first) {
                        return json_object[i];
                    }

                    results.push(json_object[i])
                }
            }
        } else {
            _.forIn(json_object, (object, key) => {
                if (object[search_field] == search_val) {
                    results.push(object);
                }
            });

            if (return_first && results.length) {
                return results[0];
            }
        }

        return results;
    },

    validEmail (email) {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },

    downloadFile(data, filename, mime) {
        // It is necessary to create a new blob object with mime-type explicitly set
        // otherwise only Chrome works like it should
        const blob = new Blob([data], {type: mime || 'application/octet-stream'});

        if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE doesn't allow using a blob object directly as link href.
            // Workaround for "HTML7007: One or more blob URLs were
            // revoked by closing the blob for which they were created.
            // These URLs will no longer resolve as the data backing
            // the URL has been freed."
            window.navigator.msSaveBlob(blob, filename);

            return;
        }

        // Other browsers
        // Create a link pointing to the ObjectURL containing the blob
        const blobURL = window.URL && window.URL.createObjectURL
            ? window.URL.createObjectURL(blob)
            : window.webkitURL.createObjectURL(blob);
        const tempLink = document.createElement('a');
        tempLink.style.display = 'none';
        tempLink.href = blobURL;
        tempLink.setAttribute('download', filename);

        // Safari thinks _blank anchor are pop ups. We only want to set _blank
        // target if the browser does not support the HTML5 download attribute.
        // This allows you to download files in desktop safari if pop up blocking
        // is enabled.
        if (typeof tempLink.download === 'undefined') {
            tempLink.setAttribute('target', '_blank');
        }

        document.body.appendChild(tempLink);
        tempLink.click();

        setTimeout(() => {
            document.body.removeChild(tempLink);

            // For Firefox it is necessary to delay revoking the ObjectURL
            window.URL.revokeObjectURL(blobURL);
        }, 100);
    },

    inArray(elem, arr, i) {
        return arr == null ? -1 : arr.indexOf(elem, i);
    },

    number_format(number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
        //  discuss at: https://locutus.io/php/number_format/
        // original by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
        // improved by: Kevin van Zonneveld (https://kvz.io)
        // improved by: davook
        // improved by: Brett Zamir (https://brett-zamir.me)
        // improved by: Brett Zamir (https://brett-zamir.me)
        // improved by: Theriault (https://github.com/Theriault)
        // improved by: Kevin van Zonneveld (https://kvz.io)
        // bugfixed by: Michael White (https://getsprink.com)
        // bugfixed by: Benjamin Lupton
        // bugfixed by: Allan Jensen (https://www.winternet.no)
        // bugfixed by: Howard Yeend
        // bugfixed by: Diogo Resende
        // bugfixed by: Rival
        // bugfixed by: Brett Zamir (https://brett-zamir.me)
        //  revised by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
        //  revised by: Luke Smith (https://lucassmith.name)
        //    input by: Kheang Hok Chin (https://www.distantia.ca/)
        //    input by: Jay Klehr
        //    input by: Amir Habibi (https://www.residence-mixte.com/)
        //    input by: Amirouche
        //   example 1: number_format(1234.56)
        //   returns 1: '1,235'
        //   example 2: number_format(1234.56, 2, ',', ' ')
        //   returns 2: '1 234,56'
        //   example 3: number_format(1234.5678, 2, '.', '')
        //   returns 3: '1234.57'
        //   example 4: number_format(67, 2, ',', '.')
        //   returns 4: '67,00'
        //   example 5: number_format(1000)
        //   returns 5: '1,000'
        //   example 6: number_format(67.311, 2)
        //   returns 6: '67.31'
        //   example 7: number_format(1000.55, 1)
        //   returns 7: '1,000.6'
        //   example 8: number_format(67000, 5, ',', '.')
        //   returns 8: '67.000,00000'
        //   example 9: number_format(0.9, 0)
        //   returns 9: '1'
        //  example 10: number_format('1.20', 2)
        //  returns 10: '1.20'
        //  example 11: number_format('1.20', 4)
        //  returns 11: '1.2000'
        //  example 12: number_format('1.2000', 3)
        //  returns 12: '1.200'
        //  example 13: number_format('1 000,50', 2, '.', ' ')
        //  returns 13: '100 050.00'
        //  example 14: number_format(1e-8, 8, '.', '')
        //  returns 14: '0.00000001'
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        const n = !isFinite(+number) ? 0 : +number
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        let s = ''
        const toFixedFix = function (n, prec) {
            if (('' + n).indexOf('e') === -1) {
                return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
            } else {
                const arr = ('' + n).split('e')
                let sig = ''
                if (+arr[1] + prec > 0) {
                    sig = '+'
                }
                return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
            }
        }
        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        }
        return s.join(dec)
    }
}
