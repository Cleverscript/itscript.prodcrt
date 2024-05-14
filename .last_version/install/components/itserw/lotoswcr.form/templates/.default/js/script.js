$(document).ready(function(){

    $.fn.serializeObject = function(){

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // Skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // Adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // Push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // Fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // Named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };

    function runItemControllerAction() {

        let filedsObject = $("#wcr-form-js").serializeObject();
        filedsObject.URL = document.location.href;

        BX.ajax.runAction('itserw:lotoswcr.Cert.add', {
            data: {
                fields: filedsObject
            }
        }).then(function (response) { // status == 'success'
            console.log(response);

            let alertBlock = document.getElementById('wcr-form-js-alert');
            let formBlock = document.getElementById('wcr-form-js');
            alertBlock.className += ' success';
            formBlock.className += ' hide';

            alertBlock.innerHTML += response.data['ALERT'];

        }, function (response) { // status !== 'success'
            console.log(response);

            let alertBlock = document.getElementById('wcr-form-js-alert');
            alertBlock.className += ' error';
            alertBlock.innerHTML = '';

            for (let i = 0; i < response.errors.length; i++) {

                let msg;

                if ((typeof response.errors[i].message) == 'string') {
                    msg = response.errors[i].message;
                } else if((typeof response.errors[i].message) == 'object') {
                    msg = response.errors[i].message[0];
                }

                alertBlock.innerHTML += msg + '<br/>';
            }

        });
    }

    function runOrderControllerAction(id) {

        var re = /^[0-9]+?$/;
		if (!re.test(id)) return;

        BX.ajax.runAction('itserw:lotoswcr.Order.get', {
            data: {
                id: id
            }
        }).then(function (response) { // status == 'success'
            
            console.log(response.data.BASKET_ITEMS);

            let items = response.data.BASKET_ITEMS;

            let resContainer = document.getElementById('res-container-js');
            let resItems = document.getElementById('order-basket-items-js');

            resItems.innerHTML = '';
            items.forEach((item) => {

                let html = '<li><label>'
                html += '<input type="checkbox" name="ITEMS[]" value="' + item.PRODUCT_ID + '"/>';
                html += '#' + item.ID + ' ' + item.NAME;
                html += ' ' + item.BASE_PRICE + ' ' + item.CURRENCY + '</label>';
                html += '</li>';

                resItems.innerHTML += html;
            })

            let alertBlock = document.getElementById('wcr-form-js-alert');
            alertBlock.className = 'wcr-form-alert';
            resContainer.style.display = 'block';


        }, function (response) { // status !== 'success'
            console.log(response);

            let alertBlock = document.getElementById('wcr-form-js-alert');
            alertBlock.className += ' error';
            alertBlock.innerHTML = '';

            for (let i = 0; i < response.errors.length; i++) {

                let msg;

                if ((typeof response.errors[i].message) == 'string') {
                    msg = response.errors[i].message;
                } else if((typeof response.errors[i].message) == 'object') {
                    msg = response.errors[i].message[0];
                }

                alertBlock.innerHTML += msg + '<br/>';
            }

        });
    }


    // Show form add cert
    /*document.getElementById('wcr-add-btn-js').addEventListener('click', (el) => {
        el.target.style.display = 'none';
        document.getElementById('wcr-form-over-js').style.display = 'block';
    });*/

    // Get order
    document.getElementById('order-id-js').addEventListener('input', (el) => {
        runOrderControllerAction(el.target.value);
    });

    // Add cert
    document.getElementById('wcr-form-btn-js').addEventListener('click', (el) => {
        runItemControllerAction();
    });

    // Rebuild events after ajax
    BX.addCustomEvent('onAjaxSuccessFinish', BX.delegate(function (element, id) {
        
        /*document.getElementById('wcr-add-btn-js').addEventListener('click', (el) => {
            el.target.style.display = 'none';
            document.getElementById('wcr-form-over-js').style.display = 'block';
        });*/

        /*document.getElementById('wcr-form-btn-js').addEventListener('click', (el) => {
            runItemControllerAction();
        });*/
       
    }));

    
});