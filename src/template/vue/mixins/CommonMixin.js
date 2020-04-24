export default {
    serialize: function (obj) {
        var str = [];
        for (let p in obj) {
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        }

        return str.length > 0 ? "?" + str.join("&") : '';
    },
    getCurrentUser: function () {
        let app = this;

        return app.$auth.user;
    },
    formatPrice(value) {
        let val = (value / 1).toFixed(2).replace('.', ',');

        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    },
    urlEncode : function(obj, prefix) {
        var str = [],
            p;
        for (p in obj) {
            if (obj.hasOwnProperty(p)) {
                var k = prefix ? prefix + "[" + p + "]" : p,
                    v = obj[p];
                str.push((v !== null && typeof v === "object") ?
                    this.urlEncode(v, k) :
                    encodeURIComponent(k) + "=" + encodeURIComponent(v));
            }
        }
        return str.join("&");
    }
    
}
